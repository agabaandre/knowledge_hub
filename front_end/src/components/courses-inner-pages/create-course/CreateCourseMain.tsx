import React from 'react';
import Link from 'next/link';
import CourseUploadTips from './CourseUploadTips';

// Import all course sections
import CourseInformationForm from '@/form/create-course/course-information-form';
import CourseSettings from './CourseSettings';
import CourseAttachment from '@/form/create-course/course-attachment';
import AddProductForm from '@/form/create-course/add-product-form';
import CoursePrerequisites from '@/form/create-course/course-prerequisites';
import CourseBuilder from './CourseBuilder';
import FeatureImageOrVideo from '@/form/create-course/feature-image-or-video';
import CertificateTemplates from './CertificateTemplates';
import AddQuizAssignment from './AddQuizAssignment';
import AdditionalData from './AdditionalData';
import CourseIntroVideo from './CourseIntroVideo';
import CourseVideoInstructors from './CourseVideoInstructors';
import AccordionItem from './AccordionItem';

// Define course sections
const courseSections = [
    { id: 'One', title: 'Course Information', component: CourseInformationForm },
    { id: 'Two', title: 'Course Settings', component: CourseSettings },
    { id: 'Three', title: 'Course Attachments', component: CourseAttachment },
    { id: 'Four', title: 'Add Product', component: AddProductForm },
    { id: 'Five', title: 'Course Prerequisites', component: CoursePrerequisites },
    { id: 'Six', title: 'Course Builder', component: CourseBuilder },
    { id: 'Seven', title: 'Featured Image and Video Lessons', component: FeatureImageOrVideo },
    { id: 'Eight', title: 'Add Quiz & Assignments', component: AddQuizAssignment },
    { id: 'Nine', title: 'Additional Data', component: AdditionalData },
    { id: 'Ten', title: 'Course Intro Video', component: CourseIntroVideo },
    { id: 'Eleven', title: 'Certificate Templates', component: CertificateTemplates },
    { id: 'Twelve', title: 'Instructors', component: CourseVideoInstructors },
];

const CreateCourseMain = () => {
    return (
        <section className="bd-new-course-area section-space">
            <div className="container">
                <div className="row g-30 justify-content-between">
                    <div className="col-xl-8 col-lg-7 order-lg-0 order-1">
                        <div className="bd-new-course-wrapper">
                            <div className="accordion-common-style accordion-transparent accordion-item-margin">
                                <div className="accordion" id="accordionExample">
                                    {courseSections.map((section) => (
                                        <AccordionItem key={section.id} {...section} />
                                    ))}
                                </div>
                            </div>
                            <div className="d-flex-items justify-content-start gap-30 mt-50">
                                <Link className="bd-btn btn-outline-secondary" href="#">Preview</Link>
                                <Link className="bd-btn btn-outline-primary" href="#">Publish</Link>
                            </div>
                        </div>
                    </div>
                    <div className="col-xl-4 col-lg-5 order-lg-1 order-0">
                        <CourseUploadTips />
                    </div>
                </div>
            </div>
        </section>
    );
};

export default CreateCourseMain;
