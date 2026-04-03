<div class="overflow-x-auto">
    <table id="datatable" class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th width="20" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300"></th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Name') }}</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('User') }}</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Server id') }}</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Product') }}</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Suspended at') }}</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">{{ __('Created at') }}</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300"></th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
        </tbody>
    </table>
</div>

<script>
    function submitResult() {
        return confirm("{{ __('Are you sure you wish to delete?') }}") !== false;
    }

    document.addEventListener("DOMContentLoaded", function() {
        $('#datatable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.3/i18n/{{ config('SETTINGS::LOCALE:DATATABLES') }}.json'
            },
            processing: true,
            serverSide: true,
            stateSave: true,
            ajax: "{{ route('admin.servers.datatable') }}{{ $filter ?? '' }}",
            order: [
                [6, "desc"]
            ],
            columns: [{
                    data: 'status',
                    name: 'servers.suspended',
                    sortable: false
                },
                {
                    data: 'name'
                },
                {
                    data: 'user',
                    name: 'user.name',
                },
                {
                    data: 'identifier'
                },
                {
                    data: 'resources',
                    name: 'product.name',
                    sortable: false
                },
                {
                    data: 'suspended'
                },
                {
                    data: 'created_at'
                },
                {
                    data: 'actions',
                    sortable: false
                },
            ],
            fnDrawCallback: function(oSettings) {
                $('[data-toggle="popover"]').popover();
            }
        });
    });
</script>