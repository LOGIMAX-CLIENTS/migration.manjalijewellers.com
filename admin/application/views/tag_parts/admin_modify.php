<!-- Admin Modify Weights Modal (included in list.php) -->

<div class="modal fade" id="adminModifyModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header ts-modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">
                    <i class="fa fa-pencil"></i> Admin — Modify Part Weights
                    <small>Partly Sale #<span id="modify_parts_id_display"></span></small>
                </h4>
            </div>

            <div class="modal-body">

                <!-- Base Tag Reference Info -->
                <div class="row" style="margin-bottom:15px;">
                    <div class="col-sm-12">
                        <div style="background:#f0f8ff; border:1px solid #d0e3f0; border-radius:6px; padding:12px 16px; display:flex; flex-wrap:wrap; gap:16px; align-items:center;">
                            <span><b style="color:#5a6a85;">Base Tag:</b> <span id="modify_base_code" style="font-weight:700; color:#2c3e50;"></span></span>
                            <span><b style="color:#5a6a85;">GWT:</b> <span id="modify_base_gwt" style="font-weight:700;"></span></span>
                            <span><b style="color:#5a6a85;">NWT:</b> <span id="modify_base_nwt" style="font-weight:700;"></span></span>
                            <span><b style="color:#5a6a85;">Stone:</b> <span id="modify_base_stone" style="font-weight:700;"></span></span>
                            <span><b style="color:#5a6a85;">Dia:</b> <span id="modify_base_dia" style="font-weight:700;"></span></span>
                            <span><b style="color:#5a6a85;">Less:</b> <span id="modify_base_less" style="font-weight:700;"></span></span>
                        </div>
                    </div>
                </div>

                <!-- Editable Child Weight Table -->
                <div class="table-responsive">
                    <table id="modify_child_table" class="table text-center child-weight-table" style="margin:0;">
                        <thead>
                            <tr style="background:linear-gradient(135deg,#1e3c72,#3c8dbc); color:#fff;">
                                <th width="5%">#</th>
                                <th width="15%">Tag Code</th>
                                <th width="14%">Gross Wt</th>
                                <th width="14%">Net Wt</th>
                                <th width="14%">Less Wt</th>
                                <th width="14%">Stone Wt</th>
                                <th width="14%">Diamond Wt</th>
                                <th width="10%">Status</th>
                            </tr>
                        </thead>
                        <tbody id="modify_child_tbody">
                            <!-- Populated via loadModifyData() -->
                        </tbody>
                        <tfoot>
                            <tr style="background:#f0f8ff; font-weight:700;">
                                <td colspan="2"><b>Part Total</b></td>
                                <td id="modify_total_gwt">0.0000</td>
                                <td id="modify_total_nwt">0.0000</td>
                                <td id="modify_total_less_wt">0.0000</td>
                                <td id="modify_total_stone_wt">0.0000</td>
                                <td id="modify_total_dia_wt">0.0000</td>
                                <td></td>
                            </tr>
                            <tr class="validation-row" style="font-weight:700;">
                                <td colspan="2"><b>Validation</b></td>
                                <td id="modify_validate_gwt"></td>
                                <td id="modify_validate_nwt"></td>
                                <td id="modify_validate_less_wt"></td>
                                <td id="modify_validate_stone_wt"></td>
                                <td id="modify_validate_dia_wt"></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div style="background:#fff8e1; border:1px solid #f0e6cc; border-radius:6px; padding:10px 14px; margin-top:12px; font-size:13px; color:#8a7240;">
                    <i class="fa fa-exclamation-triangle" style="color:#f39c12;"></i>
                    <b>Rules:</b> All weight totals must match the base tag exactly. Billed children (<i class="fa fa-lock"></i> locked) cannot be modified.
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" id="btn_save_modify" disabled>
                    <i class="fa fa-save"></i> Save Modified Weights
                </button>
            </div>

        </div>
    </div>
</div>

<input type="hidden" id="modify_parts_id" value="" />

<!-- JS for modify modal is in tag_parts.js (loaded after jQuery) -->
