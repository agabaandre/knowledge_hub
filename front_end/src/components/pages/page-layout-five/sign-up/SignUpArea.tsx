import SignUpForm from '@/form/auth/sign-up-form';
import React from 'react';
import Logo from '../../../../../public/assets/images/logo/logo.svg';
import facebook from '../../../../../public/assets/images/shape/facebook.svg';
import google from '../../../../../public/assets/images/shape/google.svg';
import Image from 'next/image';
import Link from 'next/link';

const SignUpArea = () => {
    return (
        <>
            <div className="col-xxl-6 col-xl-7">
                <div className="row justify-content-center align-items-center h100p">
                    <div className="col-xxl-7 col-xl-9 col-lg-6 col-md-6 col-sm-8 col-12">
                        <div className="bd-authentication-form-wrapper">
                            <div className="bd-authentication-form-logo">
                                <Link href="/"><Image src={Logo} alt="facebook" /></Link>
                            </div>
                            <h3 className="title mb-10">Sign Up</h3>
                            <p className="subtitle">Join us by creating a free account</p>
                            <SignUpForm />
                            <div className="bd-divider-wrapper">
                                <div className="bd-divider-line left-line"></div>
                                <span className="bd-divider-title">OR SignUp With</span>
                                <div className="bd-divider-line"></div>
                            </div>
                            <div className="bd-alter-sign mb-20">
                                <button className="bd-btn btn-outline-primary w-100" type="button"><span
                                    className="thumb"><Image style={{ width: "100%", height: "auto" }} src={facebook}
                                        alt="facebook" /></span>Facebook</button>
                                <button className="bd-btn btn-outline-secondary w-100" type="button"><span
                                    className="thumb"><Image style={{ width: "100%", height: "auto" }} src={google}
                                        alt="google" /></span>Google</button>
                            </div>
                            <div className="bd-sign-up-label underline-two text-center">
                                {`Don't`} have an account?<Link href="/sign-in" className="sign-link"> Sign In</Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
};

export default SignUpArea;