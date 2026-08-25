"use client";
import Image from "next/image";
import React from "react";
import { PhotoProvider, PhotoView } from "react-photo-view";
// Image imports
import campusGalleryOne from "../../../../../public/assets/images/gallery/campus-gallery-img-1.webp";
import campusGalleryTwo from "../../../../../public/assets/images/gallery/campus-gallery-img-2.webp";
import campusGalleryThree from "../../../../../public/assets/images/gallery/campus-gallery-img-3.webp";
import campusGalleryFour from "../../../../../public/assets/images/gallery/campus-gallery-img-4.webp";
import campusGalleryFive from "../../../../../public/assets/images/gallery/campus-gallery-img-5.webp";
import campusGallerySix from "../../../../../public/assets/images/gallery/campus-gallery-img-6.webp";

// Image data array
const galleryData = [
    { img: campusGalleryOne, title: "Main Campus", size: "big" },
    { img: campusGalleryTwo, title: "Library", size: "big" },
    { img: campusGalleryThree, title: "Sports Complex", size: "big" },
    { img: campusGalleryFour, title: "Arts Center", size: "min" },
    { img: campusGalleryFive, title: "Dormitories", size: "min" },
    { img: campusGallerySix, title: "Cafeteria", size: "min" },
];

const AboutUniversityCampusGalleryArea = () => {
    return (
        <PhotoProvider>
            <div className="bd-campus-gallery-area section-space theme-bg-05">
                <div className="container">
                    <div className="row gy-30 grid">
                        {galleryData.map((item, index) => (
                            <div
                                key={index}
                                className={`col-xl-4 col-lg-4 col-md-6 col-sm-6 grid-item`}
                            >
                                <div className={`bd-campus-gallery-${item.size}-img`}>
                                    <PhotoView src={item.img.src}>
                                        <Image src={item.img} alt={item.title} />
                                    </PhotoView>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </PhotoProvider>
    );
};

export default AboutUniversityCampusGalleryArea;
