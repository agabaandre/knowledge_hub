import { demoItemsSliderOneData, demoItemsSliderTwoData } from '@/data/Inner-page-showcase-data';
import Image from 'next/image';
import Link from 'next/link';
import React from 'react';

const InnerPageShowcasesArea = () => {
    return (
        <>
            {/* -- Start inner page Showcases area  -- */}
            <div className="inner-page-presentation-area section-space">
                <div className="container-fluid p-0">
                    <div className="row justify-content-center">
                        <div className="col-xl-8">
                            <div className="section-title-wrapper text-center section-title-space">
                                <span className="bd-section-subtitle">Explore {`iStudy's`} Inner Pages</span>
                                <h2 className="bd-section-title">105+ Versatile and Beautifully Designed Pages</h2>
                            </div>
                        </div>
                    </div>
                    <div className="row">
                        <div className="col-lg-12">
                            <div className="demo-wrapper inner-pages-wrapper">

                                <div className="row inner-pages-animation">
                                    {/* -- Start Solo Demo  -- */}
                                    {
                                        demoItemsSliderOneData.map((item, index) => (
                                            <div className="col-xl-3 col-lg-4 col-sm-6" key={index}>
                                                <div className="demo-item">
                                                    <div className="solo-demo">
                                                        <Link className="solo-demo-link" target="_blank" href={item.link}>
                                                            <div className="thumbnail">
                                                                <Image src={item.imgSrc} alt="Home Image" />
                                                            </div>
                                                            <div className="content text-center">
                                                                <h3 className="title">{item.title}</h3>
                                                            </div>
                                                        </Link>
                                                    </div>
                                                </div>
                                            </div>
                                        ))
                                    }
                                    {/* -- End Solo Demo  -- */}
                                </div>
                                <div className="row mt-30 inner-pages-animation inner-pages-animation--2">
                                    {/* -- Start Solo Demo  -- */}
                                    {
                                        demoItemsSliderTwoData.map((item, index) => (
                                            <div className="col-xl-3 col-lg-4 col-sm-6" key={index}>
                                                <div className="demo-item">
                                                    <div className="solo-demo">
                                                        <Link className="solo-demo-link" target="_blank" href={item.link}>
                                                            <div className="thumbnail">
                                                                <Image src={item.imgSrc} alt="Home Image" />
                                                            </div>
                                                            <div className="content text-center">
                                                                <h3 className="title">{item.title}</h3>
                                                            </div>
                                                        </Link>
                                                    </div>
                                                </div>
                                            </div>
                                        ))
                                    }
                                    {/* -- End Solo Demo  -- */}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {/* -- End inner page Showcases area  -- */}
        </>
    );
};

export default InnerPageShowcasesArea;