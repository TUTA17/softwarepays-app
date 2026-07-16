<div class="modal fade" id="delete-warning-modal" role="dialog" style="z-index:1060;">
    <div class="modal-dialog">
        <div class="modal-content" style="width:100%;height:100%">
            <div class="modal-header">
                <h4 class="modal-title">{{""}}</h4>
                <button type="button" class="close" data-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>{{""}}</p>
                <p>{{""}}</p>
            </div>
            <div class="modal-footer">
                <a class="btn btn-danger" id="delete-modal-yes" href="javascript:void(0)">{{""}}</a>
                <button type="button" class="btn btn-default" data-dismiss="modal">{{""}}</button>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script type="text/javascript">
        $(document).on('click', '.delete-warning', function (e) {
            e.preventDefault();
            var url = $(this).attr('href');
            $('#delete-modal-yes').attr('href', url);
            $('#delete-warning-modal').modal('show');
        });
    </script>
@endpush
