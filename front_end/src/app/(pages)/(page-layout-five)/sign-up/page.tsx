import SignUpArea from '@/components/pages/page-layout-five/sign-up/SignUpArea';
import React from 'react';
import SignInBgImage from '../../../../../public/assets/images/contact/sign-up-bg.webp';
import { Metadata } from 'next';

export const metadata: Metadata = {
    title: "Sign Up - Education & Online Courses React NextJs Template",
};

const SignUp = () => {
    return (
        <>
            {/* -- sign form area start -- */}
            <section className="bd-authentication-cover-main ">
                <div className="row h100vh mx-0">
                    <div className="col-xxl-6 col-xl-5 col-lg-12 d-xl-block d-none px-0">
                        <div className="bd-authentication-cover overflow-hidden" style={{ backgroundImage: `url(${SignInBgImage.src})` }}>
                            <div className="bd-authentication-cover-content d-flex-center d-none">
                                <div className="bd-section-title-wrapper">
                                    <h2 className="bd-section-title mb-20">Join Us Today!</h2>
                                    <p className="bd-section-paragraph">Create your account and step into a world filled with
                                        endless possibilities.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <SignUpArea />
                </div>
            </section>
            {/* -- sign form area end --  */}
        </>
    );
};

export default SignUp;