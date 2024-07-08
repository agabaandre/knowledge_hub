<div id="google_translate_element" style="display: none;"></div>
    <select class="form-control select2" onchange="translateLanguage(this.value);" style="border:#FFF;" name="langauge">
        <option value="en" {{ ('en' == $user->langauge) ? 'selected' : '' }}>English</option>
        <option value="ar" {{ ('ar' == $user->langauge) ? 'selected' : '' }}>Arabic</option>
        <option value="fr" {{ ('fr' == $user->langauge) ? 'selected' : '' }}>French</option>
        <option  value="pt" {{ ('pt' == $user->langauge) ? 'selected' : '' }}>Portuguese
        </option>
        <option value="es" {{ ('es' == $user->langauge) ? 'selected' : '' }}>Spanish</option>
        <option value="sw" {{ ('sw' == $user->langauge) ? 'selected' : '' }}>Swahili</option>


</select>