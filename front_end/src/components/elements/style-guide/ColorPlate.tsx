import React from 'react';

const ColorPlate = () => {
    return (
        <>
            <div className="bd-style-guide-color section-space-small-bottom">
                <h5 className="bd-style-guide-title mb-20">Color Palette</h5>
                <div className="row gy-30">
                    <div className="col-xl-3 col-lg-4 col-md-4 col-sm-6">
                        <div className="bd-style-color-box">
                            <div className="bd-style-color-bg theme-bg"></div>
                            <div className="content mt-15">
                                <span className="bd-style-color-code">#07A169</span>
                                <h6 className="bd-style-color-title">Primary Color</h6>
                            </div>
                        </div>
                    </div>
                    <div className="col-xl-3 col-lg-4 col-md-4 col-sm-6">
                        <div className="bd-style-color-box">
                            <div className="bd-style-color-bg secondary-bg"></div>
                            <div className="content mt-15">
                                <span className="bd-style-color-code">#FFB800</span>
                                <h6 className="bd-style-color-title">secondary-bg</h6>
                            </div>
                        </div>
                    </div>
                    <div className="col-xl-3 col-lg-4 col-md-4 col-sm-6">
                        <div className="bd-style-color-box">
                            <div className="bd-style-color-bg primary-bg"></div>
                            <div className="content mt-15">
                                <span className="bd-style-color-code">#F5F5F5</span>
                                <h6 className="bd-style-color-title">BG Primary</h6>
                            </div>
                        </div>
                    </div>
                    <div className="col-xl-3 col-lg-4 col-md-4 col-sm-6">
                        <div className="bd-style-color-box">
                            <div className="bd-style-color-bg theme-bg-secondary"></div>
                            <div className="content mt-15">
                                <span className="bd-style-color-code">#EDF3F5</span>
                                <h6 className="bd-style-color-title">BG Secondary</h6>
                            </div>
                        </div>
                    </div>
                    <div className="col-xl-3 col-lg-4 col-md-4 col-sm-6">
                        <div className="bd-style-color-box">
                            <div className="bd-style-color-bg white-bg border"></div>
                            <div className="content mt-15">
                                <span className="bd-style-color-code">#FFFFFF</span>
                                <h6 className="bd-style-color-title">White</h6>
                            </div>
                        </div>
                    </div>
                    <div className="col-xl-3 col-lg-4 col-md-4 col-sm-6">
                        <div className="bd-style-color-box">
                            <div className="bd-style-color-bg theme-black"></div>
                            <div className="content mt-15">
                                <span className="bd-style-color-code">#00170F</span>
                                <h6 className="bd-style-color-title">Theme Black</h6>
                            </div>
                        </div>
                    </div>
                    <div className="col-xl-3 col-lg-4 col-md-4 col-sm-6">
                        <div className="bd-style-color-box">
                            <div className="bd-style-color-bg full-black-bg"></div>
                            <div className="content mt-15">
                                <span className="bd-style-color-code">#000000</span>
                                <h6 className="bd-style-color-title">Full Black</h6>
                            </div>
                        </div>
                    </div>
                    <div className="col-xl-3 col-lg-4 col-md-4 col-sm-6">
                        <div className="bd-style-color-box">
                            <div className="bd-style-color-bg success-bg"></div>
                            <div className="content mt-15">
                                <span className="bd-style-color-code">#00db22</span>
                                <h6 className="bd-style-color-title">Success</h6>
                            </div>
                        </div>
                    </div>
                    <div className="col-xl-3 col-lg-4 col-md-4 col-sm-6">
                        <div className="bd-style-color-box">
                            <div className="bd-style-color-bg info-bg"></div>
                            <div className="content mt-15">
                                <span className="bd-style-color-code">#0dcaf0</span>
                                <h6 className="bd-style-color-title">Info</h6>
                            </div>
                        </div>
                    </div>
                    <div className="col-xl-3 col-lg-4 col-md-4 col-sm-6">
                        <div className="bd-style-color-box">
                            <div className="bd-style-color-bg warning-bg"></div>
                            <div className="content mt-15">
                                <span className="bd-style-color-code">#ffc107</span>
                                <h6 className="bd-style-color-title">Warning</h6>
                            </div>
                        </div>
                    </div>
                    <div className="col-xl-3 col-lg-4 col-md-4 col-sm-6">
                        <div className="bd-style-color-box">
                            <div className="bd-style-color-bg danger-bg"></div>
                            <div className="content mt-15">
                                <span className="bd-style-color-code">#FF0033</span>
                                <h6 className="bd-style-color-title">Danger</h6>
                            </div>
                        </div>
                    </div>
                    <div className="col-xl-3 col-lg-4 col-md-4 col-sm-6">
                        <div className="bd-style-color-box">
                            <div className="bd-style-color-bg gary-bg"></div>
                            <div className="content mt-15">
                                <span className="bd-style-color-code">#C5C6C7</span>
                                <h6 className="bd-style-color-title">Gary BG</h6>
                            </div>
                        </div>
                    </div>
                    <div className="col-xl-3 col-lg-4 col-md-4 col-sm-6">
                        <div className="bd-style-color-box">
                            <div className="bd-style-color-bg gradient-bg"></div>
                            <div className="content mt-15">
                                <span className="bd-style-color-code"><code>gradient-bg</code></span>
                                <h6 className="bd-style-color-title">Gradient BG</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
};

export default ColorPlate;