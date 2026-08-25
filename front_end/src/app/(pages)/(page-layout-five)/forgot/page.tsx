import React from 'react';
import ForgotPasswordBg from '../../../../../public/assets/images/contact/forgot-password-bg.webp';
import ForgotArea from '@/components/pages/page-layout-five/forgot/ForgotArea';
import { Metadata } from 'next';

export const metadata: Metadata = {
    title: "Reset Password - Education & Online Courses React NextJs Template",
};

const Forgot = () => {
    return (
        <>
            {/* -- reset-password form area start -- */}
            <section className="bd-authentication-cover-main ">
                <div className="row h100vh mx-0">
                    <div className="col-xxl-6 col-xl-5 col-lg-12 d-xl-block d-none px-0">
                        <div className="bd-authentication-cover overflow-hidden" style={{ backgroundImage: `url(${ForgotPasswordBg.src})` }}>
                            <div className="bd-authentication-cover-content d-flex-center d-none">
                                <div className="bd-section-title-wrapper">
                                    <h2 className="bd-section-title mb-20">Reset Your Password</h2>
                                    <p className="bd-section-paragraph">Enter your email to reset your password and regain
                                        access to your account.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <ForgotArea />
                </div>
            </section>
            {/* -- reset-password form area end -- */}
        </>
    );
};

export default Forgot;