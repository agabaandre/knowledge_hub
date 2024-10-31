<!-- Sharingbutton Facebook -->
<a class="resp-sharing-button__link" href="https://facebook.com/sharer/sharer.php?u={{ $link}}" target="_blank"
  rel="noopener" aria-label="Share on Facebook">
  <div class="resp-sharing-button resp-sharing-button--facebook resp-sharing-button--medium">
    <div aria-hidden="true" class="resp-sharing-button__icon resp-sharing-button__icon--solidcircle">
      <i class="fa-brands fa-facebook"></i>
    </div>
  </div>
</a>

<!-- Sharingbutton Twitter -->
<a class="resp-sharing-button__link" href="https://twitter.com/intent/tweet/?text={{ $subject}}}}url={{ $link}}"
  target="_blank" rel="noopener" aria-label="Share on X">
  <div class="resp-sharing-button resp-sharing-button--email resp-sharing-button--medium">
    <div aria-hidden="true" class="resp-sharing-button__icon resp-sharing-button__icon--solidcircle">
      <i class="fa-brands fa-x-twitter"></i>
    </div>
  </div>
</a>

<!-- Sharingbutton E-Mail -->
<a class="resp-sharing-button__link" href="mailto:?subject={{ $subject}}&body={{ $link}}" target="_self" rel="noopener"
  aria-label="Share by E-Mail">
  <div class="resp-sharing-button resp-sharing-button--email resp-sharing-button--medium">
    <div aria-hidden="true" class="resp-sharing-button__icon resp-sharing-button__icon--solidcircle">
      <i class="fa-solid fa-envelope"></i>
    </div>
  </div>
</a>

<!-- Sharingbutton LinkedIn --->
<a class="resp-sharing-button__link"
  href="https://www.linkedin.com/shareArticle?mini=true&amp;url={{ $link}}&amp;title={{ $subject}}}}summary={{ $subject}}}}source={{ $link}}"
  target="_blank" rel="noopener" aria-label="Share on LinkedIn">
  <div class="resp-sharing-button resp-sharing-button--linkedin resp-sharing-button--medium">
    <div aria-hidden="true" class="resp-sharing-button__icon resp-sharing-button__icon--solidcircle">
      <i class="fa-brands fa-linkedin"></i>
    </div>
  </div>
</a>

<!-- Sharingbutton WhatsApp -->
<a class="resp-sharing-button__link" href="whatsapp://send?text={{ $subject}} {{ $link}}" target="_blank" rel="noopener"
  aria-label="Share on WhatsApp">
  <div class="resp-sharing-button resp-sharing-button--whatsapp resp-sharing-button--medium">
    <div aria-hidden="true" class="resp-sharing-button__icon resp-sharing-button__icon--solidcircle">
      <i class="fa-brands fa-whatsapp"></i>
    </div>
  </div>
</a>