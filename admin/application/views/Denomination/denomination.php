
      						<div class="col-md-6">
      							<legend>Denomination</legend>

      							<div class="table-responsive">

								  <div class="col-md-6">
      								<table class="table table-bordered" id="denomination_CURRENCIES">
      									<tbody>
											<tr>CURRENCIES</tr>
      										<?php if (count($denomination) > 0) {
													foreach ($denomination as $d) { if($d['type'] == 1){?>
      												<tr>
      													<th width="45%" class="text-right"><?= $d['value'] ?></th>
      													<th width="10%" class="text-center">X</th>
      													<th width="45%">
      														<input class="form-control cash_count" name="cash[denomination][value][]" type="number">
      														<input type="hidden" value="<?= $d['value'] ?>" class="cash_value">
      														<input type="hidden" name="cash[denomination][id][]" value="<?= $d['id_denomination'] ?>" class="id_denomination">

															<input type="hidden" name="cash[denomination][cash_value][]" value="<?= $d['value'] ?>">
      													</th>
      												</tr>

      										<?php } }
												} ?>

      										<tr>
      											<th width="55%" colspan="2" class="text-right">Total :</th>
      											<th width="45%">
      												<input class="form-control" readonly type="number" id="total_denomination_amount_curr" name="cash[total_denomination_amount]">
      											</th>
      										</tr>
      									</tbody>
      								</table>
									</div>
									<div class="col-md-6">
									<table class="table table-bordered" id="denomination_COINS">
      									<tbody>
											<tr>COINS</tr>
      										<?php if (count($denomination) > 0) {
													foreach ($denomination as $d) {   if($d['type'] == 2){?>
													
      												<tr>
      													<th width="45%" class="text-right"><?= $d['name'] ?></th>
      													<th width="10%" class="text-center">X</th>
      													<th width="45%">
      														<input class="form-control cash_count" name="cash[denomination][value][]" type="number">
      														<input type="hidden" value="<?= $d['value'] ?>" class="cash_value">
      														<input type="hidden" name="cash[denomination][id][]" value="<?= $d['id_denomination'] ?>" class="id_denomination">

															<input type="hidden" name="cash[denomination][cash_value][]" value="<?= $d['value'] ?>">
      													</th>
      												</tr>

      										<?php } }
												} ?>

      										<tr>
      											<th width="55%" colspan="2" class="text-right">Total :</th>
      											<th width="45%">
      												<input class="form-control" readonly type="number" id="total_denomination_amount_coin" name="cash[total_denomination_amount]">
      											</th>
      										</tr>
      									</tbody>
      								</table>
								  </div>
      							</div>

      						</div>

