
<style>

    .rating {
    display: flex;
    flex-direction: row-reverse;
    justify-content: left;
    }

    .rating > input{ display:none;}

    .rating > label {
    position: relative;
        width: 1rem;
        font-size: 1rem;
        color: orange;
        cursor: pointer;
    }
    .rating > label::before{ 
    content: "\2605";
    position: absolute;
    opacity: 0;
    }
    .rating > label:hover:before,
    .rating > label:hover ~ label:before {
    opacity: 1 !important;
    }

    .rating > input:checked ~ label:before{
        opacity:1;
    }

    .rating:hover > input:checked ~ label:before{ opacity: 0.4; }


</style>

<div class="rating">
  <input type="radio" name="rating" value="5" id="5"><label for="5">☆</label>
  <input type="radio" name="rating" value="4" id="4"><label for="4">☆</label>
  <input type="radio" name="rating" value="3" id="3"><label for="3">☆</label>
  <input type="radio" name="rating" value="2" id="2"><label for="2">☆</label>
  <input type="radio" name="rating" value="1" id="1"><label for="1">☆</label>
</div>