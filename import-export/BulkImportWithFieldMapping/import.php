<?php include('header.php'); ?>
<div class="workspace-dashboard page page-forms-elements">
    <div class="page-wrap">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 col-md-12">
                    <div class="panel panel-default panel-hovered panel-stacked mb30">
                        <div class="panel-body">
                            <div class="admin_conent_form">
                                <button type="button" class="btn bg-white js-import-student-button" data-bs-toggle="modal" data-bs-target="#staticBackdrop" data-initial-popup="1">
                                    <svg width="16" height="12" viewBox="0 0 16 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M2.4 4.8C3.2825 4.8 4 4.0825 4 3.2C4 2.3175 3.2825 1.6 2.4 1.6C1.5175 1.6 0.8 2.3175 0.8 3.2C0.8 4.0825 1.5175 4.8 2.4 4.8ZM13.6 4.8C14.4825 4.8 15.2 4.0825 15.2 3.2C15.2 2.3175 14.4825 1.6 13.6 1.6C12.7175 1.6 12 2.3175 12 3.2C12 4.0825 12.7175 4.8 13.6 4.8ZM14.4 5.6H12.8C12.36 5.6 11.9625 5.7775 11.6725 6.065C12.68 6.6175 13.395 7.615 13.55 8.8H15.2C15.6425 8.8 16 8.4425 16 8V7.2C16 6.3175 15.2825 5.6 14.4 5.6ZM8 5.6C9.5475 5.6 10.8 4.3475 10.8 2.8C10.8 1.2525 9.5475 0 8 0C6.4525 0 5.2 1.2525 5.2 2.8C5.2 4.3475 6.4525 5.6 8 5.6ZM9.92 6.4H9.7125C9.1925 6.65 8.615 6.8 8 6.8C7.385 6.8 6.81 6.65 6.2875 6.4H6.08C4.49 6.4 3.2 7.69 3.2 9.28V10C3.2 10.6625 3.7375 11.2 4.4 11.2H11.6C12.2625 11.2 12.8 10.6625 12.8 10V9.28C12.8 7.69 11.51 6.4 9.92 6.4ZM4.3275 6.065C4.0375 5.7775 3.64 5.6 3.2 5.6H1.6C0.7175 5.6 0 6.3175 0 7.2V8C0 8.4425 0.3575 8.8 0.8 8.8H2.4475C2.605 7.615 3.32 6.6175 4.3275 6.065Z" fill="#222222"/>
                                    </svg>
                                    Import Student
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade student-import-modal js-student-import-modal" data-bs-backdrop="static" data-bs-keyboard="false" id="staticBackdrop" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Import from CSV/Excel</h5>
                <button type="button" class="btn" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close">
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M14 1.41L12.59 0L7 5.59L1.41 0L0 1.41L5.59 7L0 12.59L1.41 14L7 8.41L12.59 14L14 12.59L8.41 7L14 1.41Z" fill="#91A3B8"/>
                    </svg>
                </button>
            </div>
            <div class="modal-body student-mapping">
                <?php include "popup_body.php"; ?>
            </div>
        </div>
    </div>
</div>
<?php include('footer.php'); ?>

