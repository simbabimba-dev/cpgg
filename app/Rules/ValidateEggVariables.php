<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ValidateEggVariables implements DataAwareRule, ValidationRule
{
    /**
     * All of the data under validation.
     *
     * @var array<string, mixed>
     */
    protected $data = [];

    /**
     * Set the data under validation.
     *
     * @param  array<string, mixed>  $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $eggId = $this->data['egg_id'] ?? null;
        $productId = $this->data['product_id'] ?? null;

        $egg = DB::table('eggs')->where('id', $eggId)->first();

        if (!$egg || !$productId) {
            $fail('The selected egg is invalid.');

            return;
        }

        $linkedToProduct = DB::table('egg_product')
            ->where('egg_id', $eggId)
            ->where('product_id', $productId)
            ->exists();

        if (!$linkedToProduct) {
            $fail('The selected egg is not valid for the selected product.');

            return;
        }

        $decodedEnvironment = json_decode($egg->environment, true);
        if (!is_array($decodedEnvironment)) {
            $fail('The selected egg has invalid environment metadata.');

            return;
        }

        $environment = collect($decodedEnvironment)->keyBy('env_variable');
        $eggVariablesRaw = $this->data['egg_variables'] ?? [];
        if (!is_array($eggVariablesRaw)) {
            $fail('The deployment variables payload must be an object.');

            return;
        }

        $eggVariables = collect($eggVariablesRaw);

        foreach ($eggVariables as $envKey => $envValue) {
            $definition = $environment->get($envKey);
            if (!$definition) {
                $fail("Unknown deployment variable: {$envKey}.");
                continue;
            }

            if (empty($definition['user_editable'])) {
                $fail("The deployment variable {$envKey} is not user editable.");
                continue;
            }

            if (!is_scalar($envValue) && !is_null($envValue)) {
                $fail("The deployment variable {$envKey} has an invalid value.");
            }
        }

        foreach ($environment as $envVariable) {
            $rules = (string) ($envVariable['rules'] ?? 'nullable|string');
            $defaultValue = $envVariable['default_value'] ?? null;
            $envKey = $envVariable['env_variable'] ?? '';
            $isUserEditable = !empty($envVariable['user_editable']);
            $hasSubmittedValue = $eggVariables->has($envKey);

            if (!$isUserEditable) {
                if ($this->isRequiredWithoutDefault($rules, $defaultValue)) {
                    $fail("The deployment variable {$envKey} is required and cannot be overridden.");
                }
                continue;
            }

            if (!$hasSubmittedValue && !$this->isRequiredWithoutDefault($rules, $defaultValue)) {
                continue;
            }

            $valueToValidate = $hasSubmittedValue ? $eggVariables->get($envKey) : $defaultValue;
            $this->validateVariableRules($envVariable, $valueToValidate, $fail);
        }
    }

    /**
     * Get the validation rules documentation.
     *
     * @return array<string, mixed>
     */
    public static function docs(): array
    {
        return [
            'description' => 'Each egg has its own variable rules.',
        ];
    }

    /**
     * Validate the rules for each environment variable.
     */
    private function validateVariableRules(array $envVar, $value, Closure $fail): void
    {
        $validator = Validator::make(
            [$envVar['env_variable'] => $value],
            [$envVar['env_variable'] => (string) ($envVar['rules'] ?? 'nullable|string')],
        );

        $validator->setAttributeNames([
            $envVar['env_variable'] => $envVar['env_variable'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->get($envVar['env_variable']) as $error) {
                $fail($error);
            }
        }
    }

    private function isRequiredWithoutDefault(string $rules, mixed $defaultValue): bool
    {
        return str_contains($rules, 'required') && empty($defaultValue);
    }
}
