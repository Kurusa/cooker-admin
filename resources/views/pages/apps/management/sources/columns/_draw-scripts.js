KTMenu.init();
$.fn.dataTable.ext.errMode = function ( settings, helpPage, message ) {
    console.log(message);
};
Livewire.on('success', (message) => {
    LaravelDataTables['sources-table'].ajax.reload();
});
