import { ShopFooterMenuLinks } from '@/data/footer-menu/footer-menu-data';
import { getCurrentYear } from '@/utils/dateUtils';
import Link from 'next/link';
import React from 'react';
import Logo from '../../../public/assets/images/logo/logo.svg';
import playStore from '../../../public/assets/images/icon/play-store.webp';
import appStore from '../../../public/assets/images/icon/app-store.webp';
import Image from 'next/image';

const FooterFive = () => {
    return (
        <>
            {/* -- footer area start -- */}
            <footer className="fix">
                <div className="bd-footer-area section-space primary-bg">
                    <div className="container">
                        <div className="row gy-30 justify-content-xl-between justify-content-md-start">
                            <div className="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-12">
                                <div className="bd-footer-widget footer-5-col-1">
                                    <div className="bd-footer-widget-content">
                                        <div className="bd-footer-widget-logo">
                                            <Link href="/">
                                                <Image src={Logo} priority alt="image" />
                                            </Link>
                                        </div>
                                        <p className="bd-footer-widget-description">Education focused website or template is an
                                            essential part that provides visitors. </p>
                                        <div className="bd-footer-widget-contact-info">
                                            <div className="bd-footer-widget-contact-item">
                                                <span>Phone:</span>
                                                <Link href="tell:123456789">
                                                    +123-4567-8900</Link>
                                            </div>
                                            <div className="bd-footer-widget-contact-item">
                                                <span>Email:</span>
                                                <Link href="mailto:istudy@mail.com"> istudy@mail.com</Link>
                                            </div>
                                            <div className="bd-footer-widget-contact-item">
                                                <span>Address:</span>
                                                <Link href="#"> 27 Division St, New York</Link>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {/* Loop through footer sections */}
                            {ShopFooterMenuLinks.map((footerLink, index) => (
                                <div key={index} className="col-xxl-2 col-xl-3 col-lg-3 col-md-3 col-sm-6">
                                    <div className={`bd-footer-widget ${footerLink.spacingClass}`}>
                                        <h6 className="bd-footer-widget-title">{footerLink.title}</h6>
                                        <div className="bd-footer-widget-links list-none">
                                            <ul>
                                                {footerLink.links.map((link, linkIndex) => (
                                                    <li key={linkIndex} className="underline">
                                                        <Link href={link.href}>{link.name}</Link>
                                                    </li>
                                                ))}
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            ))}
                            <div className="col-xxl-3 col-xl-3 col-lg-3 col-md-3 col-sm-6">
                                <div className="bd-footer-widget footer-5-col-5">
                                    <h6 className="bd-footer-widget-title mb-20">Stay Connected</h6>
                                    <div className="bd-footer-from-content mb-20">
                                        <div className="bd-footer-social">
                                            <div className="theme-social social-brand-color">
                                                <ul className="social-icon-list">
                                                    <li><Link className="facebook" href="https://www.facebook.com/" target="_blank"><i className="fa-brands fa-facebook-f"></i></Link>
                                                    </li>
                                                    <li><Link className="twitter" href="https://x.com/" target="_blank"><i className="fa-brands fa-x-twitter"></i></Link>
                                                    </li>
                                                    <li><Link className="linkedin" href="https://www.linkedin.com/feed/" target="_blank"><i className="fa-brands fa-linkedin-in"></i></Link>
                                                    </li>
                                                    <li><Link className="instagram" href="https://www.instagram.com/" target="_blank"><i className="fa-brands fa-instagram"></i></Link>
                                                    </li>
                                                    <li><Link className="youtube" href="https://www.youtube.com/" target="_blank"><i className="fa-brands fa-youtube"></i></Link>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <h6 className="bd-footer-widget-title mb-20">Download App</h6>
                                    <div className="bd-footer-from-content">
                                        <div className="bd-footer-app-btn d-flex align-items-center gap-15">
                                            <Link className="bd-app-btn" href="#"><Image src={playStore} style={{width:"100%", height:"auto"}} alt="play-store" /></Link>
                                            <Link className="bd-app-btn" href="#"><Image src={appStore} style={{width:"100%", height:"auto"}} alt="app-store" /></Link>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div className="bd-footer-copyright-area style-three pt-15 pb-15">
                    <div className="container">
                        <div className="row justify-content-center">
                            <div className="col-lg-8">
                                <div className="bd-footer-copyright text-center">
                                    <p className="underline">© Copyright <span>{getCurrentYear()}</span> Developed By iStudy</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
            {/* -- footer area end -- */}
        </>
    );
};

export default FooterFive;