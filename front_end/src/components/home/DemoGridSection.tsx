import React from "react";
import Link from "next/link";
import courseDemo1 from "../../../public/assets/images/landing-page/course/course-demo-1.webp";
import courseDemo2 from "../../../public/assets/images/landing-page/course/course-demo-2.webp";
import courseDemo3 from "../../../public/assets/images/landing-page/course/course-demo-3.webp";
import courseDemo4 from "../../../public/assets/images/landing-page/course/course-demo-4.webp";
import courseDemo5 from "../../../public/assets/images/landing-page/course/course-demo-5.webp";
import courseDemo6 from "../../../public/assets/images/landing-page/course/course-demo-6.webp";
import courseDemo7 from "../../../public/assets/images/landing-page/course/course-demo-7.webp";
import courseDemo8 from "../../../public/assets/images/landing-page/course/course-demo-8.webp";
import courseDemo10 from "../../../public/assets/images/landing-page/course/course-demo-10.webp";
import courseDemo11 from "../../../public/assets/images/landing-page/course/course-demo-11.webp";
import courseDemo12 from "../../../public/assets/images/landing-page/course/course-demo-12.webp";
import courseDemo13 from "../../../public/assets/images/landing-page/course/course-demo-13.webp";
import courseDemo16 from "../../../public/assets/images/landing-page/course/course-demo-16.webp";
import courseDemo14 from "../../../public/assets/images/landing-page/course/course-demo-14.webp";
import courseDemo15 from "../../../public/assets/images/landing-page/course/course-demo-15.webp";
import courseDemo17 from "../../../public/assets/images/landing-page/course/course-demo-17.webp";
import Image, { StaticImageData } from "next/image";

// Props type for DemoGridItem
interface DemoGridItemProps {
    href: string;
    imgSrc: StaticImageData;
    altText: string;
}

// Main component
const DemoGridSection: React.FC = () => {
    
    const demoGridData: DemoGridItemProps[] = [
        { href: "/courses", imgSrc: courseDemo1, altText: "course-demo-1" },
        { href: "/courses-filter-search", imgSrc: courseDemo2, altText: "course-demo-2" },
        { href: "/courses-filter-category", imgSrc: courseDemo3, altText: "course-demo-3" },
        { href: "/courses-grid", imgSrc: courseDemo4, altText: "course-demo-4" },
        { href: "/courses-grid-two", imgSrc: courseDemo5, altText: "course-demo-5" },
        { href: "/courses-grid-three", imgSrc: courseDemo6, altText: "course-demo-6" },
        { href: "/courses-grid-four", imgSrc: courseDemo7, altText: "course-demo-7" },
        { href: "/courses-grid-five", imgSrc: courseDemo8, altText: "course-demo-8" },
        { href: "/courses-list-two", imgSrc: courseDemo10, altText: "course-demo-10" },
        { href: "/courses-list-three", imgSrc: courseDemo11, altText: "course-demo-11" },
        { href: "/courses/course-details", imgSrc: courseDemo12, altText: "course-demo-12" },
        { href: "/courses/course-details-two", imgSrc: courseDemo13, altText: "course-demo-13" },
        { href: "/courses/course-details-three", imgSrc: courseDemo16, altText: "course-demo-16" },
        { href: "/kindergarten-program-details", imgSrc: courseDemo15, altText: "course-demo-15" },
        { href: "/program-details", imgSrc: courseDemo14, altText: "course-demo-14" },
        { href: "/create-course", imgSrc: courseDemo17, altText: "course-demo-17" },
    ];

    return (
        <>
            {/* -- Demo Grid Area Start -- */}
            <section className="demo-grid-area section-space">
                <div className="container">
                    <div className="row justify-content-center">
                        <div className="col-xl-6">
                            <div className="section-title-wrapper text-center section-title-space">
                                <span className="bd-section-subtitle">Course Pages</span>
                                <h2 className="bd-section-title">20+ Responsive and Dynamic Course Pages</h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div className="demo-grid">
                    {demoGridData.map((item, index) => (
                        <Link key={index} href={item.href} target="_blank" className="demo-grid-item">
                            <Image
                                src={item.imgSrc}
                                alt={item.altText}
                                style={{ width: "100%", height: "auto" }}
                            />
                        </Link>
                    ))}
                </div>
            </section>
            {/* -- Demo Grid Area End -- */}
        </>
    );
};

export default DemoGridSection;
