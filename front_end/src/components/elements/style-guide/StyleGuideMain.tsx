import Link from 'next/link';
import React from 'react';
import ColorPlate from './ColorPlate';
import Typography from './Typography';
import FormElements from './FormElements';
import Pagination from './Pagination';

const StyleGuideMain = () => {
    return (
        <>
            {/* -- style guide area start -- */}
            <section className="bd-style-guide-area section-space">
                <div className="container">
                    <div className="row gy-30">
                        <div className="col-xl-3 col-lg-3">
                            <div className="bd-style-widget-sidebar sidebar-sticky">
                                <nav className="onepagenav">
                                    <ul>
                                        <li><Link href="#colorPalette"><span>1. Color Palette</span></Link></li>
                                        <li><Link href="#typography"><span>2. Typography</span></Link></li>
                                        <li><Link href="#formElements"><span>3. Form Elements</span></Link></li>
                                        <li><Link href="#pagination"><span>4. Pagination</span></Link></li>
                                    </ul>
                                </nav>
                                <div className="content-item-content">
                                    <div className="widget-details">

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className="col-xl-9 col-lg-9">
                            <div className="bd-style-guide-box">
                                <div id="information">
                                    <div className="bd-style-guide-information section-space-small-bottom">
                                        <ul className="bd-style-info-list">
                                            <li><strong>Item Name : </strong> iStudy | Education & Online Courses Template</li>
                                            <li><strong>Version : </strong> 1.0.0</li>
                                            <li className="underline-two"><strong>Author : </strong> <Link href="https://themeforest.net/user/topylo/portfolio" target="_blank">Topylo</Link></li>
                                            <li className="underline-two"><strong>Support Tickets: </strong> <Link href="https://support.topylo.com" target="_blank">https://support.topylo.com</Link></li>
                                        </ul>
                                    </div>
                                </div>
                                <div id="colorPalette">
                                    <ColorPlate />
                                </div>
                                <div id="typography">
                                    <Typography />
                                </div>
                                <div id="formElements">
                                    <FormElements />
                                </div>
                                <div id="pagination">
                                    <Pagination />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            {/* -- style guide area end -- */}
        </>
    );
};

export default StyleGuideMain;