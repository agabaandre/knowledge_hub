"use client"
import React from 'react';
import Image, { StaticImageData } from 'next/image';
import { PhotoProvider, PhotoView } from "react-photo-view";
// Import gallery images
import galleryThum1 from '../../../public/assets/images/gallery/kids-gallery-sm-thumb-1.webp';
import galleryThum2 from '../../../public/assets/images/gallery/kids-gallery-sm-thumb-2.webp';
import galleryThum3 from '../../../public/assets/images/gallery/kids-gallery-sm-thumb-3.webp';
import galleryThum4 from '../../../public/assets/images/gallery/kids-gallery-sm-thumb-4.webp';
import galleryThum5 from '../../../public/assets/images/gallery/kids-gallery-sm-thumb-5.webp';
import galleryThum6 from '../../../public/assets/images/gallery/kids-gallery-sm-thumb-6.webp';
import bigGalleryThum1 from '../../../public/assets/images/gallery/kids-gallery-big-thumb-1.webp';
import bigGalleryThum2 from '../../../public/assets/images/gallery/kids-gallery-big-thumb-2.webp';
import bigGalleryThum3 from '../../../public/assets/images/gallery/kids-gallery-big-thumb-3.webp';
import bigGalleryThum4 from '../../../public/assets/images/gallery/kids-gallery-big-thumb-4.webp';
import bigGalleryThum5 from '../../../public/assets/images/gallery/kids-gallery-big-thumb-5.webp';
import bigGalleryThum6 from '../../../public/assets/images/gallery/kids-gallery-big-thumb-6.webp';

// Define gallery item type
interface GalleryItem {
    id: number;
    smallImage: StaticImageData;
    bigImage: StaticImageData;
    orderClass: string;
}

// Gallery data array
const galleryItems: GalleryItem[] = [
    { id: 1, smallImage: galleryThum1, bigImage: bigGalleryThum1, orderClass: 'order-lg-0' },
    { id: 2, smallImage: galleryThum2, bigImage: bigGalleryThum2, orderClass: 'order-lg-1' },
    { id: 3, smallImage: galleryThum3, bigImage: bigGalleryThum3, orderClass: 'order-lg-2 col-xl-6 col-lg-6' },
    { id: 4, smallImage: galleryThum4, bigImage: bigGalleryThum4, orderClass: 'order-lg-3 order-md-1' },
    { id: 5, smallImage: galleryThum5, bigImage: bigGalleryThum5, orderClass: 'order-lg-4 order-md-0 col-xl-6 col-lg-6' },
    { id: 6, smallImage: galleryThum6, bigImage: bigGalleryThum6, orderClass: 'order-lg-5' },
];

const GalleryArea: React.FC = () => {
    return (
        <PhotoProvider>
            <div className="bd-gallery-area section-space">
                <div className="container">
                    <div className="row gy-30">
                        {galleryItems.map((item) => (
                            <div key={item.id} className={`col-xl-3 col-lg-3 col-md-6 ${item.orderClass}`}>
                                <div className="bd-gallery-thumb">
                                    <PhotoView src={item.bigImage.src}>
                                        <Image src={item.smallImage} alt={`Gallery Image ${item.id}`} />
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

export default GalleryArea;
