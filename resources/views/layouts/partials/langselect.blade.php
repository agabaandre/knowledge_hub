  

    <style>
    .language-menu {
      list-style-type: none;
      padding: 0;
      margin: 0;
    }

    .language-menu li {
      display: inline-block;
      margin-right: 10px;
   
    }
  </style>
<!-- <div class="menu-language-menu-container">
 
      <ul id="menu-language-menu" class="language-menu">

        <li style="position:relative;" class="menu-item menu-item-gtranslate">
    <a href="#" onclick="doGTranslate('en');return false;" title="English" class="glink">
        <span class="flag-icon flag-icon-gb"></span>
    </a>
</li>
<li style="position:relative;" class="menu-item menu-item-gtranslate">
    <a href="#" onclick="doGTranslate('fr');return false;" title="French" class="glink">
        <span class="flag-icon flag-icon-fr"></span>
    </a>
</li>
<li style="position:relative;" class="menu-item menu-item-gtranslate">
    <a href="#" onclick="doGTranslate('ar');return false;" title="Arabic" class="glink nturl notranslate">
        <span class="flag-icon flag-icon-sa"></span>
    </a>
</li>
<li style="position:relative;" class="menu-item menu-item-gtranslate">
    <a href="#" onclick="doGTranslate('es');return false;" title="Spanish" class="glink nturl notranslate">
        <span class="flag-icon flag-icon-es"></span>
    </a>
</li>
<li style="position:relative;" class="menu-item menu-item-gtranslate">
    <a href="#" onclick="doGTranslate('pt');return false;" title="Portuguese" class="glink nturl notranslate">
        <span class="flag-icon flag-icon-pt"></span> 
    </a>
</li>
<li style="position:relative;" class="menu-item menu-item-gtranslate">
    <a href="#" onclick="doGTranslate('sw');return false;" title="Kiswahili" class="glink nturl notranslate">
        <span class="flag-icon flag-icon-ke"></span>
    </a>
</li>

      </ul>


    </div> -->


     <div class="menu-language-menu-container">
    
          <select class="selectpicker" data-width="140" onchange="doGTranslate(this.value);">
            <option data-content='<span class="flag-icon flag-icon-us"></span> English' value="en">English</option>
            <option data-content='<span class="flag-icon flag-icon-fr"></span> Français' value="fr">Français
            </option>
            <option data-content='<span class="flag-icon flag-icon-ar"></span> العربية' value="ar">العربية</option>
            <option data-content='<span class="flag-icon flag-icon-es"></span> Español' value="es">Español</option>
            <option data-content='<span class="flag-icon flag-icon-pt"></span> Português' value="pt">Português
            </option>
            <option data-content='<span class="flag-icon flag-icon-ke"></span> Kiswahili' value="sw">Kiswahili
            </option>
          </select>
    </div>