import demoPresentationData from '@/data/demo-presentation-data';
import Image from 'next/image';
import Link from 'next/link';
import React from 'react';

const DemoPresentationArea = () => {
    return (
        <div id="homesDemos" className="home-demo-area splash-masonary-wrapper-activatio section-space">
            <div className="container">
                <div className="row justify-content-center">
                    <div className="col-xl-8 col-lg-8">
                        <div className="section-title-wrapper text-center section-title-space">
                            <span className="bd-section-subtitle">Versatile Demos</span>
                            <h2 className="bd-section-title">6 Stunning Pre-Built Education Homepages</h2>
                        </div>
                    </div>
                </div>
                <div className="home-page-demo">
                    <div className="row gy-30 justify-content-center">
                        {demoPresentationData.map((item, index) => (
                            <div
                                key={item.id}
                                className={`item col-xxl-4 col-xl-4 col-lg-4 col-md-6 wow bdFadeInUp`}
                                data-wow-delay={`${0.3 + index * 0.1}s`}
                            >
                                <Link href={item.href} target={item.target || undefined} className={item.className || ""}>
                                    <div className="thumbnail">
                                        <Image src={item.src} style={{width:"100%", height:"auto"}} alt={item.alt} />
                                    </div>
                                    <div className="content">
                                        <h6 className="title">{item.title}</h6>
                                    </div>
                                </Link>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </div>
    );
};

export default DemoPresentationArea;
