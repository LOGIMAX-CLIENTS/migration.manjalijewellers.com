<!-- KYC Collection Modal -->
<div class="modal fade" id="kyc_collection_modal" tabindex="-1" role="dialog" aria-labelledby="kycCollectionModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="display: flex; flex-direction: column; max-height: 90vh;">
            <div class="modal-header" style="background: linear-gradient(135deg, #605ca8, #7b6cc4); color: white; border-bottom: none; padding: 15px 20px; flex-shrink: 0;">
                <h4 class="modal-title" id="kycCollectionModalLabel" style="font-weight: 600; letter-spacing: 0.5px;">
                    <i class="fa fa-id-card-o" style="margin-right: 8px;"></i> Mandatory KYC Collection
                </h4>
            </div>
            <div class="modal-body" style="overflow-y: auto; flex: 1 1 auto; padding: 20px;">

                <!-- We will wrap the KYC tab contents inside a form -->
                <form id="dynamic_kyc_modal_form" enctype="multipart/form-data">
                    <input type="hidden" name="payment_kyc_cus_id" id="dynamic_kyc_modal_cus_id" value="">
                    <input type="hidden" name="payment_kyc_scheme_id" id="dynamic_kyc_modal_scheme_id" value="">
                    <input type="hidden" name="payment_kyc_pay_amount" id="dynamic_kyc_modal_pay_amount" value="">
                    <input type="hidden" name="payment_kyc_sch_acc_id" id="dynamic_kyc_modal_sch_acc_id" value="0">
                    <div id="dynamic_kyc_modal_wrapper">
                        <!-- kyc_tab.php HTML renders here via AJAX -->
                    </div>
                </form>
            </div>
            <div class="modal-footer" style="flex-shrink: 0; border-top: 1px solid #e0e0e0; padding: 12px 20px; background: #f8f8f8;">
                <button type="button" class="btn" style="background: linear-gradient(135deg, #605ca8, #7b6cc4); color: white; border: none; padding: 8px 24px; font-size: 14px; border-radius: 4px; font-weight: 500;" id="btn_save_dynamic_kyc_modal">
                    <i class="fa fa-check-circle" style="margin-right: 5px;"></i> Submit KYC Documents
                </button>
            </div>
        </div>
    </div>
</div>
<!-- /KYC Collection Modal -->
