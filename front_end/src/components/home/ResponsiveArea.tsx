import Image from 'next/image';
import React from 'react';
import deviceImg from '../../../public/assets/images/landing-page/device.webp';

const ResponsiveArea = () => {
    return (
        <>
            {/* -- responsive area start -- */}
            <section className="bd-device-responsive section-space theme-bg-05">
                <div className="container">
                    <div className="row gy-30 justify-content-between">
                        <div className="col-lg-5">
                            <div className="bd-section-title-wrapper">
                                <span className="bd-section-subtitle">Responsive</span>
                                <h2 className="bd-section-title mb-20">Responsive & Retina All Devices</h2>
                                <p className="bd-section-paragraph">
                                    Your creation with iStudy looks gorgeous and professional on any major device with it’s
                                    responsive design. iStudy is fully responsive and will adapt itself to any mobile or tablet
                                    device.
                                </p>
                            </div>
                        </div>
                        <div className="col-lg-7">
                            <div className="bd-responsive-thumb">
                                <Image src={deviceImg} style={{ width: "100%", height: "auto" }} alt="image not found" />
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            {/* -- responsive area end -- */}
        </>
    );
};

export default ResponsiveArea;