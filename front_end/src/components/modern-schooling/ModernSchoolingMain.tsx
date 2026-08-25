import React from 'react';
import ModernSchoolingBanner from './ModernSchoolingBanner';
import OverviewArea from './OverviewArea';
import ModernSchoolingCategory from '../sliders/category/ModernSchoolingCategory';
import ModernSchoolingAboutUs from './ModernSchoolingAboutUs';
import ModernSchoolingCourseArea from './ModernSchoolingCourseArea';
import ModernSchoolingCounter from './ModernSchoolingCounter';
import ModernSchoolingFaqArea from './ModernSchoolingFaqArea';
import ModernSchollingTestimonial from '../sliders/testimonial/ModernSchollingTestimonial';
import ModernSchoolingCtaArea from './ModernSchoolingCtaArea';
import ModarnSchoolingBlogArea from './ModarnSchoolingBlogArea';
import ModernSchoolingBrandArea from './ModernSchoolingBrandArea';
import SchoolingWhyChooseAreaCommon from '../common/modern-schooling/SchoolingWhyChooseAreaCommon';

const ModernSchoolingMain = () => {
    return (
        <>
            <ModernSchoolingBanner />
            <OverviewArea />
            <ModernSchoolingCategory />
            <ModernSchoolingAboutUs />
            <ModernSchoolingCourseArea />
            <ModernSchoolingCounter />
            <section className="bd-why-choose-area section-space-top fix">
                <SchoolingWhyChooseAreaCommon />
            </section>
            <ModernSchoolingFaqArea />
            <ModernSchollingTestimonial themeBackground={'theme-bg-05'} />
            <ModernSchoolingCtaArea />
            <ModarnSchoolingBlogArea />
            <ModernSchoolingBrandArea />
        </>
    );
};

export default ModernSchoolingMain;