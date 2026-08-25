import Image from 'next/image';
import React from 'react';
import headerPrimary from '../../../public/assets/images/landing-page/header-footer/header-primary.webp';
import headerUniversity from '../../../public/assets/images/landing-page/header-footer/header-university.webp';
import headerShop from '../../../public/assets/images/landing-page/header-footer/header-shop.webp';
import headerkgFullWidth from '../../../public/assets/images/landing-page/header-footer/header-kg-full-width.webp';
import headerTranstrent from '../../../public/assets/images/landing-page/header-footer/header-transtrent.webp';
import headerTranstrentTwo from '../../../public/assets/images/landing-page/header-footer/header-transtrent-two.webp';
import footerPrimary from '../../../public/assets/images/landing-page/header-footer/footer-primary.webp';
import footerBlack from '../../../public/assets/images/landing-page/header-footer/footer-black.webp';
import footerKg from '../../../public/assets/images/landing-page/header-footer/footer-kg.webp';
import footerQuarnLarning from '../../../public/assets/images/landing-page/header-footer/footer-quran-learning.webp';

const DemoHeaderFooterArea = () => {
    return (
        <>
            {/* -- header and footer area start -- */}
            <section className="promotion__area section-space-bottom fix">
                <div className="container-fluid">
                    <div className="row g-5">
                        <div className="col-xxl-5 col-xl-5 col-lg-12">
                            <div className="promotion__item">
                                <div className="section-title-wrapper anim-wrapper section-title-space animation-style-3">
                                    <h2 className="section-title title-animation is-br-none">06 Header style</h2>
                                </div>
                                <div className="promotion__elementes">
                                    <div className="header__element">
                                        <ul>
                                            <li>
                                                <Image src={headerPrimary} style={{ width: "100%", height: "auto" }} alt="header" priority />
                                            </li>
                                            <li>
                                                <Image src={headerUniversity} style={{ width: "100%", height: "auto" }} alt="header" priority />
                                            </li>
                                            <li>
                                                <Image src={headerkgFullWidth} style={{ width: "100%", height: "auto" }} alt="header" />
                                            </li>
                                            <li>
                                                <Image src={headerTranstrent} style={{ width: "100%", height: "auto" }} alt="header" />
                                            </li>
                                            <li>
                                                <Image src={headerTranstrentTwo} style={{ width: "100%", height: "auto" }} alt="header" />
                                            </li>
                                            <li>
                                                <Image src={headerShop} style={{ width: "100%", height: "auto" }} alt="header" priority />
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className="col-xxl-7 col-xl-7 col-lg-12">
                            <div className="promotion__item">
                                <div className="section-title-wrapper anim-wrapper section-title-space animation-style-3">
                                    <h2 className="section-title title-animation is-br-none">04 Footer style</h2>
                                </div>
                                <div className="promotion__elementes">
                                    <div className="footer__element">
                                        <ul>
                                            <li>
                                                <Image src={footerPrimary} style={{ width: "100%", height: "auto" }} alt="footer" priority />
                                            </li>
                                            <li>
                                                <Image src={footerBlack} style={{ width: "100%", height: "auto" }} alt="footer" priority />
                                            </li>
                                            <li>
                                                <Image src={footerKg} style={{ width: "100%", height: "auto" }} alt="footer" />
                                            </li>
                                            <li>
                                                <Image src={footerQuarnLarning} style={{ width: "100%", height: "auto" }} alt="footer" />
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            {/* -- header and footer area end -- */}
        </>
    );
};

export default DemoHeaderFooterArea;