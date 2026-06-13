<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Responsive bootstrap 4 admin template" name="description" />
        <meta content="Coderthemes" name="author" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="<?= base_url(); ?>assets/images/hris.ico">

        <!-- Plugins css-->
        <link href="<?= base_url(); ?>assets/css/renren.css" rel="stylesheet" type="text/css" />
        <link href="https://db.onlinewebfonts.com/a/0nH393RJctHgt1f2YvZvyruY" rel="stylesheet" type="text/css"/>
        
        <style>
            .aip_generate  .rrr{
                margin-bottom:20px;
                font-family:"Calibri", sans-serif;
            }
            .aip_generate  .rrr th{
                background-color:#fff;
                color:#000;
            }
            .aip_generate  .rrr td{
                text-align:left;
            }
            .rsign{
                float:left;
                margin-right:30px;
                margin-top:30px;
                position:relative;
            }
            .rsign span{
                display:block;
                line-height:1em;
            }
            .rsign .chona{
                position:absolute;
                left:20px;
                top:30px;
            }
            .rsign .feb{
                position:absolute;
                top:40px;
                width:50px;
            }

            table.rrr {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        table.rrr th, table.rrr td {
            padding: 12px 15px;
            text-align: center;
            border: 1px solid #ddd;
            font-size: 14px;
        }

        table.rrr th {
            background-color: #4CAF50;
            color: white;
            text-transform: uppercase;
        }

        table.rrr tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        table.rrr tr:hover {
            background-color: #f1f1f1;
        }

        table.rrr td a {
            text-decoration: none;
            color: #007BFF;
            font-weight: bold;
        }

        table.rrr td a:hover {
            color: #0056b3;
        }
            @page {
            size: A4;
            margin: 50px 0;
            }
            @media print {
            html, body {
                width: 210mm;
                height: 297mm;
                font-size:14px !important;
            }

            .aip_generate .hr{
                margin:20px 0;
                width:100%;
            }

            .cert{
                width:90%;
                padding-top:1px;
            }
            .aip_generate .dra_aip th, 
            .aip_generate .dra_aip td {
                padding:10px 0;
            }

            .footer {
                position: fixed;
                margin: 0 auto; /* Only centers horizontally not vertically! */
                bottom: 0;
                width:86%;
            }
            
           
            }
        </style>
    
    </head>



    <body class="aip_generate" id="printTable">

        <div class="cert">
            <img class="logo" src="<?= base_url(); ?>assets/images/report/ke.png" alt="">
            <p style="margin-bottom:0;">
            <span class="rp">Republic of the Philippines</span><br />
                <span class="de">Department of Education</span><br />
                <span class="r">Region XI</span><br />
                <span class="r">Schools Division of Davao Oriental</span>
            </p>

            <div class="hr" style="margin:10px 0"></div>
            <h4 style="margin:40px 0"><?= $title; ?></h4>

            <table class="rrr" id='myTable'>
                <tr>
                    <td>District Name</td>
                    <td style="text-align:center">Applicant List with <br /> Requirement Checklist</td>
                    <td style="text-align:center">Applicant List with<br /> Applicant Ratings</td>
                    <!-- <td style="text-align:center">Summary of Applicants for<br /> Teacher I Positions</td> -->
                </tr>
                <?php 
                $district = $this->Common->no_cond('district');
                foreach($district as $row){
                ?>
                <tr>
                    <td><?= $row->discription; ?></td>
                    <td style="text-align:center"><a target="_blank" href="<?= base_url(); ?>Pages/dabq_reportv2_list/?d=<?= $row->discription; ?>">View</a></td>
                    <td style="text-align:center"><a target="_blank" href="<?= base_url(); ?>Pages/abd_report_multiple_district/?d=<?= $row->discription; ?>">View</a></td>
                    <!-- <td style="text-align:center"><a target="_blank" href="<?= base_url(); ?>Pages/dabq_reportv2_summary/?d=<?= $row->discription; ?>">View</a></td> -->
                </tr>
                <?php } ?>
            </table>

         
        
        
        </div>

       


        
    </body>
</html>