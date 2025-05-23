<div class="row-fluid" id="ALANI_TANIMLAYAN_AD">
           
               <div class="widget-body">
                
               <div class="span12">
               
                                     <div class="inbox-wraper inbox-option row-fluid invoice-list">
                                         <ul class="buddy-online">
                                           <table width="1146" border="0">
                                             <tr>
                                               <td width="911" ><h3><?php echo $dil["alma_talepno"]; ?><?php echo $row_teklifveren['ataTalepID']; ?></h3></td>
                                               <td width="225"><img src="../../resim/logo/<?php echo $row_ayar['Sitelogo']; ?>"></td>
                                             </tr>
                                           </table>

                                       </ul>
                                      </div>
                 </div>
                 </div>
             


             <div class="widget-body">


               <div class="span12">
                                     <div class="inbox-wraper row-fluid invoice-list">
                                         <ul class="buddy-online">
                                         
<table class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th width="14%">&nbsp;</th>
                                      <td width="31%"><h5><strong>Teklif isteyen ticari bilgileri</strong></h5>
                                      
                                      <ul class="unstyled">
                                        <li><strong>Şirket Adı:</strong>".$odeme=$_POST['sirketAdi']."</li>
                                        <li><strong>Adres:</strong><?php echo $_POST['Adres']; ?><br>
                                        <?php echo $row_teklifveren['sehir']; ?>
                                        <?php echo $row_teklifveren['Ulke']; ?>
                                        
                                        <li><strong>İletişim:</strong>$_POST['HesapYoneticisiTitle']</li>
                                        <li><strong>Mail:</strong>".$_POST['HesapYoneticisiMail']."</li>
                                        <li><strong>Tel:</strong><?php echo $row_teklifveren['Tel']; ?></li>
                                        </ul>
                                        
                                        </td>
                                        <th width="16%" class="hidden-480">&nbsp;</th>
                                        <th width="25%" class="hidden-480"><h5><strong>Teklif atanin ticari bilgileri</strong></h5>
                                          <ul class="unstyled">
                                            <li><strong>Şirket Adı:</strong><?php echo $row_teklifalan['sirketAdi']; ?></li>
                                            <li><strong>Adres:</strong><?php echo $row_teklifalan['Adres']; ?><br>
                                              <?php echo $row_teklifalan['sehir']; ?> <?php echo $row_teklifalan['Ulke']; ?>
                                            <li><strong>İletişim:</strong><?php echo $row_teklifalan['HesapYoneticisiTitle']; ?></li>
                                            <li><strong>Mail:</strong><?php echo $row_teklifalan['HesapYoneticisiMail']; ?></li>
                                            <li><strong>Tel:</strong><?php echo $row_teklifalan['Tel']; ?></li>
                                        </ul></th>
                                       
                                        <td width="14%"><h5>&nbsp;</h5>
                                          <ul class="unstyled">
                                            <li><strong>Teslim Süresi:</strong><?php echo $row_talep['toplateslimsure']; ?></li>
                                            <li><strong>Teslim Şekli:</strong><?php echo $row_talep['toplateslimsekli']; ?></li>
                                            <li><strong>Ödeme Şekli:</strong><?php echo $row_talep['toplaodemekosul']; ?></li>
                                            <li><strong>Ödeme Süresi:</strong><?php echo $row_talep['toplaodemevadesi']; ?></li>
                                          </ul>
                                        
                                        </td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                                         </ul>
                                    </div>
                 </div>
                                 
                        
              </div>
                            
      

                 <div class="widget-body">
       
                                 <div class="span12">
                                     <div class="inbox-wraper inbox-option">
                                     <ul class="buddy-online">
<table class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th width="32%"><span class="hidden-480">Ürün tanımı</span></th>
                                        <th width="30%" class="hidden-480">Refrans No</th>
                                        <th width="23%" class="hidden-480">Birim Fiyat</th>
                                        <th width="15%" class="hidden-480">Adet</th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td><?php echo $row_atabilgiler['uruntanim']; ?></td>
                                        <td class="hidden-480"><?php echo $row_atabilgiler['referansno']; ?></td>
                                        <td class="hidden-480"><?php echo $row_talep['toplafiyat']; ?><?php echo $row_atabilgiler['parabirim']; ?></td>
                                        <td class="hidden-480"><?php echo $row_atabilgiler['forecast']; ?></td>
                                      </tr>
                                    </tbody>
                                </table>
                            
                            <div class="space20"></div>
                            <div class="row-fluid">
                                <div class="span4 invoice-block pull-right">
                                    <ul class="unstyled amounts">
                                        <li><strong>Toplam :</strong> <?php 
										$sonuc=$row_talep['toplafiyat']*$row_atabilgiler['forecast'];?>
										<?php echo $sonuc; ?><?php echo $row_atabilgiler['parabirim']; ?>
										
										</li>
                                    
                                    </ul>
                                </div>
                            </div>
                            </ul>
                            <div class="space20"></div>

                            <div class="row-fluid text-center">
                                
                                <a onClick="birAlaniYazdir()" class="btn btn-success btn-large hidden-print">Print <i class="icon-print icon-big"></i></a>
                            
                                   </div>
                                 </div> 
                 </div>       
     
    </div> 
     </div>