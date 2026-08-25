import React from 'react';
import Image from 'next/image';
import brandImg1 from '../../../public/assets/images/brand/brand-img-1.webp';
import brandImg3 from '../../../public/assets/images/brand/brand-img-3.webp';
import brandImg4 from '../../../public/assets/images/brand/brand-img-4.webp';
import brandImg5 from '../../../public/assets/images/brand/brand-img-5.webp';
import brandImg6 from '../../../public/assets/images/brand/brand-img-6.webp';
import sopnsorLogo from '../../../public/assets/images/brand/sopnsor-logo-1.webp';
import sopnsorLogo2 from '../../../public/assets/images/brand/sopnsor-logo-2.webp';

const TrustedPartners = () => {
    return (
        <>
            {/* -- partner area start -- */}
            <div className="bd-partner-area section-space-small">
                <div className="container">
                    <div className="bd-partner-area-bg">
                        <div className="row justify-content-center">
                            <div className="col-xxl-10 col-xl-8 col-lg-10 col-md-10">
                                <div className="bd-section-title-wrapper mb-30 text-center">
                                    <h4 className="bd-section-title text-primary">Trusted by</h4>
                                </div>
                                <div className="bd-brand-wrapper style-six">
                                    <div className="bd-trusted-partner">
                                        <Image src={brandImg1} alt="Brand 1" />
                                    </div>
                                    <div className="bd-trusted-partner">
                                        <Image src={brandImg3} alt="Brand 2" />
                                    </div>
                                    <div className="bd-trusted-partner">
                                        <Image src={brandImg4} alt="Brand 3" />
                                    </div>
                                    <div className="bd-trusted-partner">
                                        <Image src={brandImg5} alt="Brand 4" />
                                    </div>
                                    <div className="bd-trusted-partner">
                                        <Image src={brandImg6} alt="Brand 5" />
                                    </div>
                                    <div className="bd-trusted-partner">
                                        <Image src={sopnsorLogo} alt="Brand 6" />
                                    </div>
                                    <div className="bd-trusted-partner">
                                        <Image src={sopnsorLogo2} alt="Brand 7" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {/* -- partner area end -- */}
        </>
    );
};

export default TrustedPartners;
