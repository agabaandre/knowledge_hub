import React from 'react';
// import preloaderIcon from "../../../../public/assets/images/logo/preloader-icon.webp";
import Image from 'next/image';

const Preloader = () => {
    return (
        <>
            <div id="loading">
                <div id="loading-center">
                    <div id="loading-center-absolute">
                        <div className="bd-preloader-content">
                            <div className="bd-preloader-logo">
                                <div className="bd-preloader-circle">
                                    <svg width={190} height={190} viewBox="0 0 380 380" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle stroke="#F5F5F5" cx="190" cy="190" r="180" strokeWidth="6" strokeLinecap="round">
                                        </circle>
                                        <circle stroke="red" cx="190" cy="190" r="180" strokeWidth="6" strokeLinecap="round">
                                        </circle>
                                    </svg>
                                </div>
                                <Image
                                src="/assets/images/logo/preloader-icon.webp"
                                alt="icon"
                                priority
                                width={80}
                                height={80}
                            />
                            </div>
                            <p className="bd-preloader-subtitle">Loading...</p>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
};

export default Preloader;