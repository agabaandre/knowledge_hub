import React from 'react';
import FinancialFeaturesList from './FinancialFeatureList';

const ScholarshipFinancialDetailsArea = () => {
    return (
        <>
            <section className="bd-financial-details-area section-space">
                <div className="container">
                    <div className="row justify-content-center">
                        <div className="col-xl-12 col-lg-12">
                            <div className="bd-financial-details-content">
                                <div className="bd-section-title-wrapper section-title-space">
                                    <h3 className="bd-section-title mb-20">
                                        UNDERGRADUATE STUDENT FINANCIAL ASSISTANCE POLICY (SUMMER 2024)
                                    </h3>
                                    <p className="bd-financial-aid-desc">
                                        This policy will be applicable for students enrolled from Summer 2022. The previous
                                        rules/policy will remain applicable for the students registered till Spring 2022,
                                        except
                                        for the category ‘IUB Merit Scholarship based on the semester result’. (The policy
                                        change under the IUB Merit Scholarship category will be applicable for all the
                                        undergraduates across the University from Autumn 2022).
                                    </p>
                                </div>
                                <div className="bd-financial-feature-box">
                                    <h3 className="bd-details-content-title">
                                        Categories of Financial Assistance for Undergraduate Students
                                    </h3>
                                    <p>
                                        IUB offers financial assistance in the following formats:
                                    </p>
                                    <div className="bd-financial-feature-list mb-15">
                                        <ul>
                                            <li>A. Merit Scholarship</li>
                                            <li>B. Financial Aid</li>
                                            <li>C. Tuition Fee Discount</li>
                                        </ul>
                                    </div>
                                    <p>
                                        If a student qualifies for two scholarships or discounts, they will be eligible to
                                        enjoy only the one with the higher amount.
                                    </p>
                                </div>

                                <div className="bd-financial-feature-box">
                                    <h3 className="bd-details-content-title">A. MERIT SCHOLARSHIP</h3>
                                    <p>
                                        The Merit Scholarship is awarded based on academic performance. The top students
                                        from each department are selected for this scholarship.
                                    </p>
                                    <table className="table table-striped">
                                        <thead className="table-success">
                                            <tr>
                                                <th>CGPA bar for
                                                    reassessment (in subsequent semesters)</th>
                                                <th>Percentage</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>3.80-3.84</td>
                                                <td>30%</td>
                                            </tr>
                                            <tr>
                                                <td>3.85-3.89</td>
                                                <td>50%</td>
                                            </tr>
                                            <tr>
                                                <td>3.90-3.99</td>
                                                <td>75%</td>
                                            </tr>
                                            <tr>
                                                <td>4.00</td>
                                                <td>100%</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <p>
                                        Students qualifying for multiple scholarships or discounts will receive only the one
                                        with the higher benefit.
                                    </p>
                                </div>

                                <div className="bd-financial-feature-box">
                                    <h3 className="bd-details-content-title">B. FINANCIAL AID</h3>
                                    <p>
                                        Financial aid is available to students with demonstrated financial need. This
                                        assistance is determined after a thorough evaluation of the {`student's`} and their
                                        family’s financial status.
                                    </p>
                                    <div className="bd-financial-feature-list">
                                        <ul>
                                            <li>Percentage of tuition covered: Up to 50% based on need.</li>
                                            <li>Documentation required: Income statements, family details, and other
                                                financial information.</li>
                                        </ul>
                                    </div>
                                </div>

                                <div className="bd-financial-feature-box">
                                    <h3 className="bd-details-content-title">C. TUITION FEE DISCOUNT</h3>
                                    <p>
                                        Tuition fee discounts are available to students based on specific criteria, such as
                                        family relations (siblings enrolled at the same time), employee status (children of
                                        IUB employees), or alumni.
                                    </p>
                                    <div className="bd-financial-feature-list">
                                        <ul>
                                            <li>Sibling discount: 10% for each sibling enrolled.</li>
                                            <li>Employee benefit: 20%-50% for children of university employees.</li>
                                            <li>Alumni discount: 5% for students who are dependents of alumni.</li>
                                        </ul>
                                    </div>
                                </div>

                                <h3 className="bd-details-content-title">Terms and Conditions for Financial Assistance</h3>
                                <FinancialFeaturesList />
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </>
    );
};

export default ScholarshipFinancialDetailsArea;