"use client"
import React from 'react';
import image1 from '../../../../../public/assets/images/gallery/campus-gallery-img-1.webp';
import image2 from '../../../../../public/assets/images/gallery/campus-gallery-img-2.webp';
import image3 from '../../../../../public/assets/images/gallery/campus-gallery-img-3.webp';
import image4 from '../../../../../public/assets/images/gallery/campus-gallery-img-4.webp';
import image5 from '../../../../../public/assets/images/gallery/campus-gallery-img-5.webp';
import image6 from '../../../../../public/assets/images/gallery/campus-gallery-img-6.webp';
import image7 from '../../../../../public/assets/images/gallery/campus-gallery-img-7.webp';
import image8 from '../../../../../public/assets/images/gallery/campus-gallery-img-8.webp';
import image9 from '../../../../../public/assets/images/gallery/campus-gallery-img-9.webp';
import image10 from '../../../../../public/assets/images/gallery/campus-gallery-img-10.webp';

import { PhotoProvider, PhotoView } from "react-photo-view";
import Image from 'next/image';
import Link from 'next/link';
import Breadcrumbs from '@/components/common/Breadcrumb/Breadcrumbs';
import AboutCtaArea from '@/components/common/about-cta/AboutCtaArea';

const imageCategories = [
    {
        category: "Snapshots of Campus",
        image: image1,
        images: [image2, image3, image4, image5],
    },
    {
        category: "Celebrating Campus Traditions",
        image: image2,
        images: [image6, image7, image8, image9, image10],
    },
    {
        category: "Memorable Campus Events",
        image: image3,
        images: [image1, image10, image9, image7],
    },
    {
        category: "Student Life Through the Lens",
        image: image4,
        images: [image1, image2, image3, image4, image5],
    },
    {
        category: "Vibrant Campus Moments",
        image: image5,
        images: [image8, image4, image3, image4, image9],
    },
    {
        category: "Adventures Across Campus",
        image: image6,
        images: [image1, image2, image3, image4, image5],
    },
    {
        category: "Cherished Campus Memories",
        image: image7,
        images: [image3, image5, image7, image1],
    },
    {
        category: "Our Campus, Our Home",
        image: image8,
        images: [image1, image2, image3, image4, image5, image6, image7, image8, image9, image10],
    },
];

const GalleryMain = () => {
    return (
        <>
            <Breadcrumbs breadcrumbTitle='Gallery' />
            {/* -- gallery area start -- */}
            <PhotoProvider>
                <section className="bd-campus-gallery-area section-space">
                    <div className="container">
                        <div className="row justify-content-center">
                            <div className="col-xl-8 col-lg-8">
                                <div className="bd-section-title-wrapper section-title-space text-center">
                                    <span className="bd-section-subtitle">Our Moments Captured</span>
                                    <h2 className="bd-section-title">Discover the Beauty in Every Frame</h2>
                                </div>
                            </div>
                        </div>
                        <div className="row g-30 justify-content-center">
                            {imageCategories.map((item, index) => (
                                <div key={index} className="col-xxl-3 col-xl-4 col-lg-4 col-md-6">
                                    <div className="bd-campus-gallery-wrapper">
                                        <PhotoView src={item.image.src}>
                                            <div className="bd-campus-gallery-thumb">
                                                <Image src={item.image} alt="image" />
                                            </div>
                                        </PhotoView>
                                        <h6 className="bd-campus-gallery-title">{item.category}</h6>
                                    </div>
                                </div>
                            ))}
                        </div>

                        {/* -- gallery-more style -- */}
                        <div className="bd-gallery-more-btn d-flex justify-content-center mt-50">
                            <Link className="bd-btn btn-outline-border-primary" href="#">Load More <span className="right-icon"><i
                                className="fa-duotone fa-spinner"></i></span></Link>
                        </div>
                        {/* -- gallery-more style end -- */}
                    </div>
                </section>
            </PhotoProvider>
            {/* -- gallery area end -- */}
            {/* -- About cta area start -- */}
            <AboutCtaArea />
        </>
    );
};

export default GalleryMain;
