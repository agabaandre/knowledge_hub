<div class="container-fluid">
  <div class="row">
    <div class="col-12 col-lg-6 d-none d-lg-flex col-img">
    </div>
    <div class="col-12 col-lg-6 align-self-center">
      <div class="m-3 m-lg-5">

        <div class="text-center mt-4 mb-3">
          <h1 class="h3">Multi-step Form Wizard</h1>
          <p class="lead">
            Navigate between steps in this multi-step Bootstrap 5 Form Wizard example.
          </p>
        </div>

        <ul id="steps-native" class="nav nav-pills justify-content-center"></ul>
        
        <form id="wizard" class="my-2 py-2">
          <section data-step="1. Account">
            <div class="mb-3">
              <label class="form-label" for="email">Email</label>
              <input class="form-control" type="email" name="email" placeholder="Email.." required>
            </div>
            <div class="mb-3">
              <label class="form-label" for="password">Password</label>
              <input class="form-control" type="password" name="password" placeholder="Password.." required>
            </div>
            <div class="row">
              <div class="col-12 text-right">
                <button class="btn btn-primary" data-next>Next</button>
              </div>
            </div>
          </section>
        
          <section data-step="2. Profile">
            <div class="mb-3">
              <label class="form-label" for="first-name">First name</label>
              <input class="form-control" type="text" name="first-name" placeholder="First name.." required>
            </div>
            
            <div class="mb-3">
              <label class="form-label" for="last-name">Last name</label>
              <input class="form-control" type="text" name="last-name" placeholder="Last name.." required>
            </div>

            <div class="row">
              <div class="col-6 text-left">
                <button class="btn btn-outline-primary" data-prev>Previous</button>
              </div>
              <div class="col-6 text-right">
                <button class="btn btn-primary" data-next>Next</button>
              </div>
            </div>
          </section>
        
          <section data-step="3. Social">
            <div class="mb-3">
              <label class="form-label" for="facebook">Facebook</label>
              <input class="form-control" type="date" name="facebook" placeholder="Facebook.." required>
            </div>
            <div class="mb-3">
              <label class="form-label" for="twitter">Twitter</label>
              <input class="form-control" type="text" name="twitter" placeholder="Twitter.." required>
            </div>
            <div class="mb-3">
              <label class="form-label" for="instagram">Instagram</label>
              <input class="form-control" type="text" name="instagram" placeholder="Instagram.." required>
            </div>
        
            <div class="row">
              <div class="col-6 text-left">
                <button class="btn btn-outline-primary" data-prev>Previous</button>
              </div>
              <div class="col-6 text-right">
                <button class="btn btn-primary" type="submit">Sign up</button>
              </div>
            </div>
          </section>
        </form>
      </div>
    </div>
  </div>
</div>