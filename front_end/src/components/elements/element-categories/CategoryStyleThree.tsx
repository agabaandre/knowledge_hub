import Link from 'next/link';
import React from 'react';

// Define a type for the category object
interface Category {
    title: string;
    icon: string;
    colorClass: string;
}
// Category data
const categories: Category[] = [
    { title: 'Quranic Tafsir', icon: 'fas fa-book-quran', colorClass: 'color-primary' },
    { title: 'Hadith Studies', icon: 'fas fa-scroll', colorClass: 'color-secondary' },
    { title: 'Islamic Fiqh', icon: 'fas fa-scale-balanced', colorClass: 'color-extra-01' },
    { title: 'Arabic Language', icon: 'fas fa-language', colorClass: 'color-extra-02' },
    { title: 'Quran Memorization', icon: 'fas fa-moon', colorClass: 'color-extra-05' },
    { title: 'Islamic History', icon: 'fas fa-landmark', colorClass: 'color-extra-06' },
];

const CategoryStyleThree = () => {
    return (
        <>
            {/* -- category style 03 start -- */}
            <section className="bd-elements-category section-space-bottom">
                <div className="container">
                    <div className="row align-items-center justify-content-center">
                        <div className="col-lg-12">
                            <div className="bd-elements-section-wrapper section-title-space text-center">
                                <div className="bd-elements-line">
                                    <div className="bd-separator-line line-left"></div>
                                    <div className="bd-elements-title-wrapper">
                                        <h2 className="bd-elements-title small">Categories Style 03</h2>
                                    </div>
                                    <div className="bd-separator-line line-right"></div>
                                </div>
                                <p className=""></p>
                            </div>
                        </div>
                    </div>
                    <div className="row gy-30">
                        {categories.map((category, index) => (
                            <div key={index} className="col-xl-2 col-lg-2 col-md-3 col-sm-6">
                                <Link href="#" className="bd-category-wrapper style-three">
                                    <div className="bd-category-item">
                                        <div className={`bd-category-icon-wrapper ${category.colorClass}`}>
                                            <span className="bd-category-icon">
                                                <i className={category.icon}></i>
                                            </span>
                                        </div>
                                        <h6 className="bd-category-title">{category.title}</h6>
                                    </div>
                                </Link>
                            </div>
                        ))}
                    </div>
                </div>
            </section>
            {/* -- category style 03 end -- */}
        </>
    );
};

export default CategoryStyleThree;
