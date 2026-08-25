import { CourseFooterMenuLinks } from '@/data/footer-menu/footer-menu-data';
import Image from 'next/image';
import Link from 'next/link';
import React from 'react';
import logoSvg from "../../../public/assets/images/logo/logo-white.svg";
import { getCurrentYear } from '@/utils/dateUtils';

const FooterTwo = () => {
    return (
        <>
            {/* -- footer area start -- */}
            <footer>
                <div className="bd-footer-area style-two has-footer-space theme-black fix">
                    <div className="container">
                        <div className="row gy-30 justify-content-between">
                            {/* Footer Logo and Social Icons */}
                            <div className="col-xxl-3 col-xl-3 col-lg-3 col-md-6 col-sm-12">
                                <div className="bd-footer-widget footer-2-col-1">
                                    <div className="bd-footer-widget-logo">
                                        <Link href="/">
                                            <Image src={logoSvg} priority alt="logo" />
                                        </Link>
                                    </div>
                                    <div className="bd-footer-widget-content">
                                        <p className="bd-footer-widget-description">
                                            Education focused website or template is an essential part that provides visitors with insights into the program or service offered.
                                        </p>
                                        <div className="bd-footer-social">
                                            <div className="theme-social has-white">
                                                <ul className="social-icon-list">
                                                    <li><Link href="https://www.facebook.com/" target="_blank"><i className="fa-brands fa-facebook-f"></i></Link></li>
                                                    <li><Link href="https://x.com/" target="_blank"><i className="fa-brands fa-x-twitter"></i></Link></li>
                                                    <li><Link href="https://www.linkedin.com/feed/" target="_blank"><i className="fa-brands fa-linkedin-in"></i></Link></li>
                                                    <li><Link href="https://www.instagram.com/" target="_blank"><i className="fa-brands fa-instagram"></i></Link></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {/* Loop through footer sections */}
                            {CourseFooterMenuLinks.map((footerLink, index) => (
                                <div key={index} className="col-xxl-2 col-xl-2 col-lg-2 col-md-3 col-sm-4">
                                    <div className={`bd-footer-widget ${footerLink.spacingClass}`}>
                                        <h6 className="bd-footer-widget-title">{footerLink.title}</h6>
                                        <div className="bd-footer-widget-links">
                                            <ul>
                                                {footerLink.links.map((link, linkIndex) => (
                                                    <li key={linkIndex} className="underline-two">
                                                        <Link href={link.href}>{link.name}</Link>
                                                    </li>
                                                ))}
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            ))}

                            {/* Newsletter Section */}
                            <div className="col-xxl-3 col-xl-3 col-lg-3 col-md-8 col-sm-12">
                                <div className="bd-footer-widget footer-2-col-5">
                                    <h6 className="bd-footer-widget-title">Newsletter</h6>
                                    <div className="bd-footer-from-content">
                                        <div className="bd-footer-widget-subscribe">
                                            <p className="bd-footer-widget-description">Subscribe our newsletter to get the latest news & updates.</p>
                                            <form action="#">
                                                <div className="bd-footer-subscribe-form style-two">
                                                    <input type="email" placeholder="Email address" />
                                                    <button className="bd-btn btn-primary h-40px" type="submit">Subscribe</button>
                                                </div>
                                            </form>
                                            <div className="checkout-agree">
                                                <div className="checkout-option">
                                                    <input id="read_all" type="checkbox" />
                                                    <label htmlFor="read_all">I have read and agree to the website.</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Footer Copyright Section */}
                <div className="bd-footer-copyright-area style-two theme-bg fix bd-noise-bg">
                    <div className="container">
                        <div className="row justify-content-between">
                            <div className="col-xl-12">
                                <div className="bd-footer-copyright-text text-center">
                                    <p className="underline-two">© Copyright <span>{getCurrentYear()}</span> |  Developed By iStudy.</p>
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

export default FooterTwo;
