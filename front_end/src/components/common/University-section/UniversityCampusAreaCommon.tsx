import AcademicBuildingSvg from '@/svg/campus/AcademicBuildingSvg';
import ArtsSvg from '@/svg/campus/ArtsSvg';
import LibrariesSvg from '@/svg/campus/LibrariesSvg';
import Link from 'next/link';
import React from 'react';
//image
import image1 from '../../../../public/assets/images/campus/campus-life-01.webp';
import image2 from '../../../../public/assets/images/campus/campus-life-02.webp';
import image3 from '../../../../public/assets/images/campus/campus-life-03.webp';
import Image from 'next/image';

const UniversityCampusAreaCommon = () => {
    // Define campus data
    const campusItems = [
        {
            id: 1,
            title: 'Academic Buildings',
            image: image1,
            SvgComponent: AcademicBuildingSvg,
        },
        {
            id: 2,
            title: 'Libraries',
            image: image2,
            SvgComponent: LibrariesSvg,
        },
        {
            id: 3,
            title: 'Arts and Culture',
            image: image3,
            SvgComponent: ArtsSvg,
        },
    ];

    return (
        <>
            {/* -- campus area start -- */}
            <section className="bd-campus-area section-space">
                <div className="container">
                    <div className="row justify-content-center">
                        <div className="col-xl-6">
                            <div className="bd-section-wrapper section-title-space text-center">
                                <span className="bd-section-subtitle">Exceptional Amenities</span>
                                <h2 className="bd-section-title mb-20">
                                    Explore Our <span className="down-mark-line">Campus</span> Life
                                </h2>
                            </div>
                        </div>
                    </div>
                    <div className="row g-30">
                        {/* Render campus items dynamically */}
                        {campusItems.map((item) => (
                            <div className="col-xl-4 col-lg-4 col-md-6" key={item.id}>
                                <div className="bd-campus-wrapper style-one">
                                    <div className="bd-campus-item">
                                        <div className="bd-campus-thumb">
                                            <Link href="/campus">
                                                <Image src={item.image} priority style={{ width: "100%", height: "auto" }} alt={item.title} />
                                            </Link>
                                        </div>
                                        <div className="bd-campus-content">
                                            <div className="bd-campus-info">
                                                <h5 className="bd-campus-title underline">
                                                    <Link href="/campus">{item.title}</Link>
                                                </h5>
                                                <div className="bd-campus-btn underline">
                                                    <Link href="/campus">Read More</Link>
                                                </div>
                                            </div>
                                            <div className="bd-campus-icon">
                                                <item.SvgComponent />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </section>
            {/* -- campus area end -- */}
        </>
    );
};

export default UniversityCampusAreaCommon;
