
    <!--==============================content================================-->
    <section id="content">
      <div class="wrapper">
        <article class="col-1">
          <div class="indent-left">
            <h3 class="p1">Bize ulaşın...</h3>
            


            <form id="fomr" action="phpmail/index.php" method="post" enctype="multipart/form-data">
              
              <fieldset>
                
                <label><span class="text-form">Ad Soyad:</span>
                  <input type="text" name="isim">
                </label>

                <label><span class="text-form">Mail:</span>
                  <input type="text" name="eposta">
                </label>

                <label><span class="text-form">Telefon:</span>
                  <input type="text" name="telefon">
                </label>

                 <label><span class="text-form">Konu:</span>
                  <input type="text" name="konu">
                </label>

                <div class="wrapper">
                  <div class="text-form">Message:</div>
                  <div class="extra-wrap">
                    <textarea type="text" name="mesaj"></textarea>
                  </div>
                </div>
                <br>
                <button type="submit" name="iletisimform" class="button-2" >Mail Gönder</button>
              </fieldset>
            </form>


          </div>
        </article>
   
      </div>
      <div class="block"></div>
    </section>
  </div>
</div>
