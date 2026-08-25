import BreadcrumbsTwo from '@/components/common/Breadcrumb/BreadcrumbsTwo';
import React from 'react';
import Link from 'next/link';
import ApplicationForm from '@/form/ApplicationForm';

const ApplyOnlineMain = () => {
    return (
        <>
            <BreadcrumbsTwo breadcrumbTwoTitle='Online Application Form' />
            <section className="bd-apply-form-area form-max section-space">
                <div className="container custom-container">
                    <div className="row justify-content-center">
                        <div className="col-xl-12">
                            <div className="bd-apply-form">
                                <div className="bd-apply-form-top">
                                    <h3 className="bd-course-details-content-title text-center">Before filling up the Admission Form</h3>
                                    <ul>
                                        <li>Please read the <Link href="#" className="text-primary">Instructions and Admission
                                            Eligibility</Link> carefully.</li>
                                        <li>Ensure all mandatory fields marked with (*) are filled out correctly.</li>
                                        <li>Keep scanned copies of necessary documents (e.g., Passport, National ID, Birth
                                            Certificate) ready for upload.</li>
                                        <li>Double-check the information entered before submission to avoid discrepancies.
                                        </li>
                                        <li>Ensure the contact details provided are up-to-date for future correspondence.
                                        </li>
                                        <li>Review the Admission Terms and Conditions before finalizing your submission.
                                        </li>
                                    </ul>
                                </div>
                                <div className="bd-form-divider"></div>
                                <ApplicationForm />
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </>
    );
};

export default ApplyOnlineMain;