# PR #3 Code Review - Issues Tracker

Use this as a checklist to track fixes for identified issues.

---

## Critical Priority (🔴 Must Fix Before Merge)

### Issue #1: Coupon Validation Exploit
- [ ] **File**: `app/Http/Controllers/Admin/CouponController.php:176-178`
- [ ] **Action**: Replace custom validation with proper integer validation
- [ ] **Code Change**: 
  ```php
  'max_uses' => [
      'required',
      'integer', 
      function ($attribute, $value, $fail) {
          if ($value !== -1 && ($value < 1 || $value > 999999999)) {
              $fail(__('Max uses must be -1 for unlimited or between 1 and 999,999,999.'));
          }
      }
  ],
  ```
- [ ] **Test**: Create coupon with max_uses = -1, 0, 1, 999999999, 1000000000
- [ ] **Assignee**: _____________
- [ ] **Status**: ⬜ Not Started | ⬜ In Progress | ⬜ Done

---

### Issue #2: Product Swap Validation
- [ ] **File**: `app/Http/Controllers/Admin/ProductController.php:85-92, 165-172`
- [ ] **Action**: Fix swap validation in both store() and update() methods
- [ ] **Code Change**:
  ```php
  'swap' => 'required|integer|min:-1|max:999999999',
  ```
- [ ] **Test**: Create product with swap = -1, 0, 1024, 999999999
- [ ] **Assignee**: _____________
- [ ] **Status**: ⬜ Not Started | ⬜ In Progress | ⬜ Done

---

### Issue #3: Currency Conversion in UnsuspendServers
- [ ] **File**: `app/Listeners/UnsuspendServers.php:23`
- [ ] **Action**: Remove hardcoded division or use Currency facade
- [ ] **Code Change**:
  ```php
  $this->min_credits_to_make_server = $user_settings->min_credits_to_make_server ?? 0;
  ```
- [ ] **Test**: Verify server unsuspension threshold is correct
- [ ] **Assignee**: _____________
- [ ] **Status**: ⬜ Not Started | ⬜ In Progress | ⬜ Done

---

## High Priority (🟠 Should Fix Before Merge)

### Issue #4: Coupon Usage Race Condition
- [ ] **File**: `app/Listeners/CouponUsed.php:37-44`
- [ ] **Action**: Implement atomic coupon usage tracking
- [ ] **Database**: Consider adding `uses_count` column to `user_coupons` pivot table
- [ ] **Code Change**: Use `lockForUpdate()` or database transactions
- [ ] **Test**: Concurrent coupon usage test (100 simultaneous requests)
- [ ] **Assignee**: _____________
- [ ] **Status**: ⬜ Not Started | ⬜ In Progress | ⬜ Done

---

### Issue #5: Currency Operation Error Handling
- [ ] **File**: `app/Http/Controllers/Admin/SettingsController.php:174-176`
- [ ] **Action**: Add validation and error handling for currency conversions
- [ ] **Code Change**: 
  ```php
  if (in_array($key, $currencyKeys) && $inputValue !== null && $inputValue !== '') {
      if (!is_numeric($inputValue)) {
          throw new \InvalidArgumentException("Invalid numeric value for {$key}");
      }
      try {
          $inputValue = Currency::prepareForDatabase($inputValue);
      } catch (\Exception $e) {
          Log::error("Currency conversion failed for {$key}: " . $e->getMessage());
          throw $e;
      }
  }
  ```
- [ ] **Test**: Try saving settings with non-numeric currency values
- [ ] **Assignee**: _____________
- [ ] **Status**: ⬜ Not Started | ⬜ In Progress | ⬜ Done

---

### Issue #6: Boolean Checkbox Handling
- [ ] **File**: `app/Http/Controllers/Admin/SettingsController.php:154`
- [ ] **Action**: Simplify boolean assignment
- [ ] **Code Change**: `$settingsClass->$key = $request->has($key);`
- [ ] **Test**: Save boolean settings with checkbox checked/unchecked
- [ ] **Assignee**: _____________
- [ ] **Status**: ⬜ Not Started | ⬜ In Progress | ⬜ Done

---

### Issue #7: Per-User Coupon Limit Logic
- [ ] **File**: `app/Models/Coupon.php:99-117`
- [ ] **Action**: Fix coupon limit checking logic
- [ ] **Code Change**: Use dependency injection, fix usage counting
- [ ] **Test**: Test per-user limits with unlimited global coupons
- [ ] **Assignee**: _____________
- [ ] **Status**: ⬜ Not Started | ⬜ In Progress | ⬜ Done

---

## Medium Priority (🟡 Consider for This PR)

### Issue #8: Settings Casts Migration
- [ ] **Files**: `app/Settings/UserSettings.php`, `app/Settings/ReferralSettings.php`
- [ ] **Action**: Create migration to convert existing currency data
- [ ] **OR**: Re-enable casts and handle conversion on input/output
- [ ] **Assignee**: _____________
- [ ] **Status**: ⬜ Not Started | ⬜ In Progress | ⬜ Done

---

### Issue #9: Missing .git/HEAD Logging
- [ ] **File**: `app/Providers/AppServiceProvider.php:69-87`
- [ ] **Action**: Add logging when .git/HEAD is missing
- [ ] **Assignee**: _____________
- [ ] **Status**: ⬜ Not Started | ⬜ In Progress | ⬜ Done

---

### Issue #10: Unlimited Display Inconsistency
- [ ] **Files**: Multiple Blade templates
- [ ] **Action**: Standardize on "Unlimited" or "∞" symbol
- [ ] **Recommendation**: Create helper function for consistency
- [ ] **Assignee**: _____________
- [ ] **Status**: ⬜ Not Started | ⬜ In Progress | ⬜ Done

---

## Low Priority (🟢 Nice to Have / Follow-up PR)

### Issue #11: Code Formatting
- [ ] **Action**: Run PHP CS Fixer with project standards
- [ ] **Assignee**: _____________
- [ ] **Status**: ⬜ Not Started | ⬜ In Progress | ⬜ Done

---

### Issue #12: Missing Translations
- [ ] **Files**: Blade templates
- [ ] **Action**: Wrap hardcoded strings in `__()` helper
- [ ] **Assignee**: _____________
- [ ] **Status**: ⬜ Not Started | ⬜ In Progress | ⬜ Done

---

### Issue #13: Commented Code Removal
- [ ] **Files**: Settings files
- [ ] **Action**: Remove commented code blocks
- [ ] **Assignee**: _____________
- [ ] **Status**: ⬜ Not Started | ⬜ In Progress | ⬜ Done

---

## Testing Checklist

### Required Tests Before Merge
- [ ] Unlimited coupons (max_uses = -1) work correctly
- [ ] Concurrent coupon usage doesn't bypass limits (stress test)
- [ ] Products with unlimited resources (0 or -1) save/display correctly
- [ ] All currency conversions work (settings, display, database)
- [ ] Settings save/load correctly with currency fields
- [ ] Coupon validation rejects invalid values
- [ ] Product validation rejects invalid swap values

### Recommended Tests for Follow-up
- [ ] Coupon expiration with unlimited uses
- [ ] Edge cases for all new features
- [ ] Per-user limits with global unlimited coupons
- [ ] Server unsuspension with correct credit thresholds

---

## Database Migrations Needed

### Required
- [ ] None for current functionality

### Recommended for Enhancement
- [ ] Add `uses_count` column to `user_coupons` pivot table
  ```sql
  ALTER TABLE user_coupons ADD COLUMN uses_count INT DEFAULT 0;
  ```
- [ ] Create migration for currency settings format conversion

---

## Review Sign-off

- [ ] All critical issues (#1-3) resolved
- [ ] All high priority issues (#4-7) resolved
- [ ] Test coverage added
- [ ] Code review approved
- [ ] Ready for production deployment

**Reviewer**: _________________ **Date**: _________  
**Developer**: _________________ **Date**: _________  
**QA**: _________________ **Date**: _________

---

## Notes

_Use this space for additional notes, blockers, or decisions made during fixes:_

---

**Generated from Code Review**: 2025-11-09  
**Source**: CODEREVIEW_PR3.md
