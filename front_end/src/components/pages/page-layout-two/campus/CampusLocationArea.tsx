import React from 'react';

const CampusLocationArea = () => {
    return (
        <>
            <section className="bd-location-area section-space-bottom">
                <div className="container">
                    <div className="row">
                        <div className="col-xl-12">
                            <div className="bd-section-title-wrapper section-title-space">
                                <h3 className="bd-section-title bottom-line">Location</h3>
                            </div>
                        </div>
                    </div>
                    <div className="row">
                        <div className="col-xl-12">
                            <div className="bd-campus-view">
                                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3168.3995816270603!2d-122.1726349236816!3d37.427664232078364!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x808fbb2a678bea9d%3A0x29cdf01a44fc687f!2sStanford%20University!5e0!3m2!1sen!2sbd!4v1727155102819!5m2!1sen!2sbd" width="600" height="400" style={{border: '0'}} allowFullScreen loading="lazy" referrerPolicy="no-referrer-when-downgrade">
                                </iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </>
    );
};

export default CampusLocationArea;