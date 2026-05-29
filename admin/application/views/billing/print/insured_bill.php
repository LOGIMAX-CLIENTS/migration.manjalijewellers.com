<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Vinsmera — Exact XLSX Design (use header.png)</title>
<style>
    @media print {
        @page {
            size: A4;
            margin: 1cm;
        }

        body {
            width: 210mm;
            height: 297mm;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .letterhead img {
            height: auto;
            width: 100% !important;
            display: block;
        }
    }

    .letterhead {
        width: 100%;
        text-align: center;
    }

    .letterhead img {
        height: auto;
        display: inline-block;
    }

    /* Reset & base */
    html,body { margin:0; padding:0; font-family: "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; color:#222; background:#f4f6f8; }
    .policy-container {
        padding: 5px 15px;
        margin: 0 auto;
        background-color: white;
    }

    .section-title {
        font-size: 12px;
        font-weight: bold;
        margin: 3px 0;
        color: #000;
    }

    /* Print styling */
    @media print {
        body { background: white; }
        .policy-container {
                    margin: 0;
                    padding: 5px 15px;
                }
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: Arial, sans-serif;
    }

    li {
        margin-bottom: 1px;
    }

    body {
        padding: 20px;
        background-color: #f5f5f5;
    }
    
    .certificate-container {
        margin: 0 auto;
        background-color: white;
        padding: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .header {
        text-align: center;
        font-size: 14px;
        line-height: 1.4;
    }
    
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 2px;
        font-size: 14px;
    }
    
    td {
        padding: 8px 10px;
        border: 1px solid #000;
        vertical-align: top;
    }

    .terms-list{
        font-size: 11px;
    }

    .note{
        font-size: 14px;
        margin: 10px 0;
    }
</style>
</head>
<body>
    <div class="letterhead">
        <img src="<?php echo base_url();?>/assets/img/insurance_head.jpg" alt="Vinsmera Insurance letterhead featuring the Vinsmera logo on a blue and white background, company name and tagline displayed prominently, conveying a professional and trustworthy tone">
    </div>
    <div class="certificate-container">
        <div class="header">
            <center><b>3rd FLOOR, ADITYA TOWER, OPPOSITE R.T.O., KANNUR - 670 002, KERALA.</b></center>
        </div>
        <?php
            function moneyFormatIndia($num)
			{
				return preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $num);
			}
        ?>
        <table>
            <tr>
                <td>Insurance Certificate No:</td>
                <td><?php echo $insurance['insurance_no']; ?></td>
                <td>Date</td>
                <td><?php echo date('d-m-Y', strtotime($insurance['policy_from'])); ?></td>
            </tr>
            <tr>
                <td>Name of the Insured Member</td>
                <td><?php echo $billing['customer_name']; ?></td>
                <td>Invoice No</td>
                <td><?php echo $billing['invoice_no']; ?></td>
            </tr>
            <tr>
                <td>Address of the Insured Member</td>
                <td>
                    <?php
                        $addressParts = [];
                        if (!empty($billing['address1'])) $addressParts[] = $billing['address1'];
                        if (!empty($billing['address2'])) $addressParts[] = $billing['address2'];
                        if (!empty($billing['address3'])) $addressParts[] = $billing['address3'];
                        if (!empty($billing['city'])) $addressParts[] = $billing['city'];
                        if (!empty($billing['state'])) $addressParts[] = $billing['state'];
                        if (!empty($billing['pincode'])) $addressParts[] = $billing['pincode'];
                        echo implode(', ', $addressParts);
                    ?>
                </td>
                <td>Invoice Value (INR)</td>
                <td><?php echo moneyFormatIndia($insurance['insurance_amount']);?></td>
            </tr>
            <tr>
                <td>Policy Period</td>
                <td><?php echo date('d-m-Y', strtotime($insurance['policy_from'])); ?></td>
                <td>To</td>
                <td><?php echo date('d-m-Y', strtotime($insurance['policy_to'])); ?></td>
            </tr>
            <tr>
                <td>Store Name and Address</td>
                <td colspan="3">
                    <?php echo $comp_details['company_name']." ".$comp_details['address1']." ".$comp_details['address2']." ".$comp_details['address3']." ".$comp_details['city']." ".$comp_details['state']." - ".$comp_details['pincode'] ?>
                    <!-- Vinsmera Jewels, M/s. Win Gold LLP-Branch Calicut, 29/21(G), 22(F), Pottammal Jn, Mayor Road, Calicut - 673 016, Kerala. -->
                </td>
            </tr>
            <tr style="height:80px;">
                <td colspan="2" style="vertical-align:bottom;">Customer Signature</td>
                <td colspan="2" style="vertical-align:bottom;">
                    <img src="<?php echo base_url();?>/assets/img/sign.png" alt=""/><br>
                    For The New India Assurance Co Ltd.
                </td>
            </tr>
        </table>
        <div class="note">
            <b>Note :</b> The policy is valid only for brand new purchases made as described in the invoice number mentioned above.
        </div>
        <div class="section-title"><b>COVERAGE</b></div>
        <div class="policy-container">
            
            <div class="section">
                <ol class="terms-list">
                    <li>The policy covers loss or damage to the insured items, by fire, riot & strike, malicious damage, burglary, theft, snatching.</li>
                    <li>Cover attaches only when the purchased item is in the custody of insured/spouse/children/parents/siblings of the insured person/Grandchildren/Grandparents of the insured person.</li>
                    <li>Cover extended to all purchases as per the invoice.</li>
                    <li>Maximum liability of the company, in no case shall exceed the sum insured.</li>
                </ol>
            </div>
        </div>
        <div class="section-title"><b>EXCLUSIONS</b></div>
        <div class="policy-container">
            <div class="section">
                <ol class="terms-list">
                    <li>Damage Caused by any process of cleaning, repairing, renovation or deterioration arising from wear & tear.</li>
                    <li>Breakage, cracking, scratching of stonework and inherent/ manufacturing defects of the jewellery.</li>
                    <li>By war& nuclear perils, intentional damage/loss by insured or by his / her relatives.</li>
                    <li>Theft from unattended vehicles.</li>
                    <li>Theft of insured property while remaining unattended in public places/vehicles.</li>
                    <li>Unexplained losses, shortages due to error or omissions, losses discovered when making an inventory or a periodic stock taking of any property or induced to do so by deception.</li>
                    <li>Loss or damage caused by wear and tear or gradual deterioration.</li>
                    <li>Loss or damage where any inmate or member of the Insured's household or of his business staff or any other person lawfully in the premises is concerned in the actual theft of or damage to any of the articles or premises or where such loss or damage has been expedited or in any way assisted or brought about by any such person or persons</li>
                    <li>Permanent or temporary dispossession resulting from confiscation, commandeering or requisition by any lawfully constituted authority</li>
                    <li>Loss by Misfortune (Fortuitous Losses, Loss due to carelessness)</li>
                </ol>
            </div>
        </div>    
        <div class="section-title"><b>TERMS AND CONDITIONS</b></div>
        <div class="policy-container">
            <div class="section">
                <ol class="terms-list">
                    <li>In case of Theft/ Burglary/ Fire claim, immediate complaint should be lodged with the appropriate Govt Authorities and the following documents should be submitted to the insurance company</li>
                    <li>Copy of such intimation, FIR and other reports issued by appropriate authority in English</li>
                    <li>Final Investigation Report, Non – traceable certificate and order of Judicial Magistrate in English</li>
                    <li>Claim form duly filled in along with the Original certificate of insurance and sales invoice.</li>
                    <li>Any other documents in support of the claim .</li>
                    <li>Pair and set clause: If any part of the pair/ set is lost/ damaged Company shall pay only the value of such parts lost/ damaged and not the full value of the pair / set.</li>
                    <li>Claim intimation should be with in 30 days from the date of loss</li>
                    <li>Geographical limit-Claims happening in Indian Territory only</li>
                    <li>Claim Settlement process: wherever the final report/ non traceable report is not forthcoming after 120 days and the surveyor validating the claim- will settle up to 50% of the admissible amount</li>
                    <li>Excess : 2.50% of the claim amount subject to minimum of Rs. 2,500/-</li>
                    <li>If Loss is occuring from premises which has been left uninhabited by day and night for seven or more consecutive days and nights while the premises shall have been left uninhabited/li>
                </ol>
            </div>
        </div>    
    </div>
</body>
<footer>
    <center>
        <b><p>All claims will be processes by The New India Assurance Co.Ltd., Non-suit claims Hub, Grand Plaza, Fort Road, Kannur - 670 001.</p>
        <p>To intimate claim contact : 0497-2700599, Mail: nia.769005@newindia.co.in, nia.760800@newindia.co.in.</p></b>
    </center>
</footer>
</html>