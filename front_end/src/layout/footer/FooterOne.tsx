import Link from 'next/link';
import React from 'react';
import topyloImg from '../../../public/assets/images/landing-page/topylo.png';
import Image from 'next/image';
import { getCurrentYear } from '@/utils/dateUtils';
import footerBgImg from "../../../public/assets/images/bg/error-bg.webp";
import { Parallax } from 'react-parallax';

const FooterOne = () => {
    return (
        <>
            {/* -- footer area start -- */}
            {/* Parallax wrapper */}
            <Parallax
                bgImage={footerBgImg.src}
                strength={400}
            >
            <footer className="demo-footer-area demo-footer-bg section-space" style={{backgroundImage:`url(${footerBgImg.src})`}}>
                <div className="container">
                    <div className="row align-items-center justify-content-center">
                        <div className="col-xl-12 col-lg-12 col-md-12">
                            <div className="demo-footer-wrapper text-center">
                                <span className="bd-section-subtitle">One-Time Payment for Lifetime Update</span>
                                <h2 className="demo-footer-title white-text mb-20">Start Your Education Website with <span
                                    className="theme-text">iStudy</span></h2>
                                <span className="d-block white-text mb-35">Elevate your property listings with iStudy, the pinnacle of
                                    modern educational websites design.</span>
                                <div className="demo-banner-btn">
                                    <Link className="bd-btn btn-primary btn-extra-large" href="https://themeforest.net/user/topylo">
                                        Purchase iStudy
                                    </Link>
                                </div>
                                <div className="topylo-logo mt-30">
                                    <Link href="https://themeforest.net/user/topylo/portfolio" target="_blank">
                                        <Image src={topyloImg} style={{width:"100%", height:"auto"}} alt="topylo" />
                                    </Link>
                                </div>
                                <p className="white-text d-none">All rights reserved @ <span>{getCurrentYear()}</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
            </Parallax>
            {/* -- footer area end -- */}
        </>
    );
};

export default FooterOne;