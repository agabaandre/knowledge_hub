import ForgotForm from '@/form/auth/forgot-form';
import Image from 'next/image';
import Link from 'next/link';
import React from 'react';
import Logo from '../../../../../public/assets/images/logo/logo.svg';

const ForgotArea = () => {
    return (
        <>
            <div className="col-xxl-6 col-xl-7">
                <div className="row justify-content-center align-items-center h100p">
                    <div className="col-xxl-7 col-xl-9 col-lg-6 col-md-6 col-sm-8 col-12">
                        <div className="bd-authentication-form-wrapper">
                            <div className="bd-authentication-form-logo">
                                <Link href="/"><Image src={Logo} alt="logo" /></Link>
                            </div>
                            <h3 className="title mb-10">Reset Password</h3>
                            <p className="subtitle">Hello Wick</p>
                            <ForgotForm />
                            <div className="bd-divider-wrapper">
                                <div className="bd-divider-line left-line"></div>
                                <span className="bd-divider-title">OR Remember?</span>
                                <div className="bd-divider-line"></div>
                            </div>
                            <div className="bd-sign-up-label underline-two text-center">
                                {`Don't`} have an account?<Link href="/sign-up" className="sign-link"> Sign up</Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
};

export default ForgotArea;