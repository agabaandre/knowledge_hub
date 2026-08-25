import Image from 'next/image';
import React from 'react';
import detailsImage from '../../../../../public/assets/images/program/details-2.webp';

const InformationArea = () => {
    return (
        <>
        {/* -- program details area start here  -- */}
        <section className="bd-program-details-widget section-space-bottom">
            <div className="container">
                <div className="row gy-30">
                    <div className="col-lg-6">
                        <div className="bd-program-details-slider p-relative">
                            <div className="bd-program-details-slider-thumb p-relative">
                                <Image src={detailsImage} alt="images"/>
                            </div>
                        </div>
                    </div>
                    <div className="col-lg-6">
                        <div className="bd-program-details-widget-content theme-bg-05">
                            <h3 className="bd-program-details-widget-title">More Information</h3>
                            <p className="mb-25">The past thirteen years have been memorable for the free kinder garten movement in
                                the United States. Previous to that time, the work was largely private, experimental.</p>
                            <div className="bd-program-details-list">
                                <ul>
                                    <li>Semester start and end dates, holidays</li>
                                    <li>Accreditation & Bear Facts</li>
                                    <li>Graduate Division</li>
                                    <li>Research at Our School</li>
                                    <li>Textbooks: Cal Student Store</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        {/* -- program details area end here  -- */}
        </>
    );
};

export default InformationArea;