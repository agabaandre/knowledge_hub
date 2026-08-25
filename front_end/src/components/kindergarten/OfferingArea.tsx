import React from "react";
import Image, { StaticImageData } from "next/image";
import BookIcon from "../../../public/assets/images/icon/book.svg";
import LearnIcon from "../../../public/assets/images/icon/learn.svg";
import ArtIcon from "../../../public/assets/images/icon/art.svg";
import BulbIcon from "../../../public/assets/images/icon/bulb-2.svg";
import OfferingThumb1 from "../../../public/assets/images/offer/offering-thumb-1.webp";
import OfferingThumb2 from "../../../public/assets/images/offer/offering-thumb-2.webp";
import OfferingThumb3 from "../../../public/assets/images/offer/offering-thumb-3.webp";
import OfferingThumb4 from "../../../public/assets/images/offer/offering-thumb-4.webp";
import Link from "next/link";

interface IOfferings {
    icon: StaticImageData;
    title: string;
    image: StaticImageData;
    bgClass: string
}
const offerings: IOfferings[] = [
    {
        icon: BookIcon,
        title: "Imaginative Stories",
        image: OfferingThumb1,
        bgClass: "bg-1",
    },
    {
        icon: LearnIcon,
        title: "Play to Learn",
        image: OfferingThumb2,
        bgClass: "bg-2",
    },
    {
        icon: ArtIcon,
        title: "Art in Many Forms",
        image: OfferingThumb3,
        bgClass: "bg-3",
    },
    {
        icon: BulbIcon,
        title: "Nature’s Inspiration",
        image: OfferingThumb4,
        bgClass: "bg-4",
    },
];

const OfferingArea = () => {
    return (
        <section className="bd-offering-area section-space">
            <div className="container">
                <div className="row justify-content-center">
                    <div className="col-xl-5 col-lg-8">
                        <div className="bd-section-title-wrapper section-title-space text-center">
                            <h2 className="bd-section-title mb-20">Our Offerings</h2>
                            <p className="bd-section-paragraph">
                                Our multi-level kindergarten programs cater to the age group of 2-5 year switch a curriculum focussing children.
                            </p>
                        </div>
                    </div>
                </div>
                <div className="row gy-30">
                    {offerings.map((offering, index) => (
                        <div key={index} className="col-xl-3 col-lg-6 col-md-6">
                            <div className="bd-offering-wrapper style-one">
                                <div className={`bd-offering-item ${offering.bgClass}`}>
                                    <div className="content">
                                        <div className="icon">
                                            <Image style={{ width: "auto", height: "auto" }} src={offering.icon} alt={offering.title} />
                                        </div>
                                        <h5 className="title">
                                            <Link href="#">{offering.title}</Link>
                                        </h5>
                                    </div>
                                    <div className="thumb">
                                        <Image style={{ width: "auto", height: "auto" }} src={offering.image} alt={offering.title} />
                                    </div>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            </div>
        </section>
    );
};

export default OfferingArea;
