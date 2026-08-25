"use client"
import Image from 'next/image';
import React from 'react';
import bgPrevImg from '../../public/assets/images/bg/privew-thumb.webp';
import bgSignatureImg from '../../public/assets/images/bg/signature.webp';
import { bloodGroup, countryItems, maritalStatusItems, nationalityItems, programSubject, religionItems, semesterItems } from '@/data/dropdown-data';
import { Gender } from '@/data/nice-select-data';
import NiceSelect from '@/components/elements/nice-select/NiceSelect';


const ApplicationForm = () => {
    const selectHandler = () => { }

    return (
        <>
            <form action="#">
                <div className="row gy-30">
                    <div className="col-xxl-8 col-xl-8 col-lg-8">
                        <div className="form-input-box">
                            <div className="form-input-title">
                                <label htmlFor="programSubject" data-error="wrong" data-success="right">Program/Subject <span>*</span></label>
                            </div>
                            <div className="form-input">
                                <div className="form-select-option">
                                    <NiceSelect
                                        options={programSubject}
                                        defaultCurrent={0}
                                        onChange={selectHandler}
                                        filterIcon={false}
                                        name=""
                                        className="course-orderby"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="col-xxl-4 col-xl-4 col-lg-4">
                        <div className="form-input-box">
                            <div className="form-input-title">
                                <label htmlFor="semesterSelect" data-error="wrong" data-success="right">Semester <span>*</span></label>
                            </div>
                            <div className="form-input">
                                <div className="form-select-option">
                                    <NiceSelect
                                        options={semesterItems}
                                        defaultCurrent={0}
                                        onChange={selectHandler}
                                        filterIcon={false}
                                        name=""
                                        className="course-orderby"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="col-lg-6 col-md-12">
                        <div className="table-responsive">
                            <table className="bd-apply-form-table table table-borderless ">
                                <tbody>
                                    <tr>
                                        <td>{`Applicant's`} Name (English) <span className="required">(*)</span>
                                        </td>
                                        <td><input name="applicant_name_english" id="applicantNameEnglish" type="text" placeholder="Applicant's Name (English)" required /></td>
                                    </tr>
                                    <tr>
                                        <td>Gender <span className="required">(*)</span></td>
                                        <td>
                                            <div className="form-select-option">
                                                <NiceSelect
                                                    options={Gender}
                                                    defaultCurrent={0}
                                                    onChange={selectHandler}
                                                    filterIcon={false}
                                                    name=""
                                                    className="course-orderby"
                                                />
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{`Father's`} Name <span className="required">(*)</span></td>
                                        <td><input name="father_name" id="fatherName" type="text" placeholder="Father's Name" required /></td>
                                    </tr>
                                    <tr>
                                        <td>{`Mother's`} Name <span className="required">(*)</span></td>
                                        <td><input name="mother_name" id="motherName" type="text" placeholder="Mother's Name" required /></td>
                                    </tr>
                                    <tr>
                                        <td>{`Guardian's`} Name</td>
                                        <td><input name="guardian_name" id="guardianName" type="text" placeholder="Guardian's Name (Optional)" /></td>
                                    </tr>
                                    <tr>
                                        <td>{`Applicant's`} Mobile Number <span className="required">(*)</span>
                                        </td>
                                        <td><input name="applicant_mobile" id="applicantMobile" type="text" placeholder="Mobile Number" required /></td>
                                    </tr>
                                    <tr>
                                        <td>{`Applicant's`} Email</td>
                                        <td><input name="applicant_email" id="applicantEmail" type="email" placeholder="Email" /></td>
                                    </tr>
                                    <tr>
                                        <td>Nationality <span className="required">(*)</span></td>
                                        <td>
                                            <div className="form-select-option">
                                                <NiceSelect
                                                    options={nationalityItems}
                                                    defaultCurrent={0}
                                                    onChange={selectHandler}
                                                    filterIcon={false}
                                                    name=""
                                                    className="course-orderby"
                                                />
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Marital Status <span className="required">(*)</span></td>
                                        <td>
                                            <div className="form-select-option">
                                                <NiceSelect
                                                    options={maritalStatusItems}
                                                    defaultCurrent={0}
                                                    onChange={selectHandler}
                                                    filterIcon={false}
                                                    name=""
                                                    className="course-orderby"
                                                />
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Passport No (If any)</td>
                                        <td><input name="passport_no" id="passportNo" type="text" placeholder="Passport No. (Optional)" /></td>
                                    </tr>
                                    <tr>
                                        <td>National ID No.</td>
                                        <td><input name="national_id_no" id="nationalIdNo" type="text" placeholder="National ID No." /></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div className="col-lg-6 col-md-12">
                        <div className="table-responsive">
                            <table className="bd-apply-form-table table table-borderless ">
                                <tbody>
                                    <tr>
                                        <td style={{ width: '40%' }}>{`Applicant's`} Name (Local)</td>
                                        <td style={{ width: '60%' }}><input name="applicant_name_local" id="applicantNameLocal" type="text" placeholder="Applicant's Name (Local)" /></td>
                                    </tr>
                                    <tr>
                                        <td>Date of Birth <span className="required">(*)</span></td>
                                        <td><input name="dob" id="dateOfBirth" type="date" required />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{`Father's`} Occupation <span className="required">(*)</span></td>
                                        <td><input name="father_occupation" id="fatherOccupation" type="text" placeholder="Father's Occupation" required />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{`Mother's`} Occupation <span className="required">(*)</span></td>
                                        <td><input name="mother_occupation" id="motherOccupation" type="text" placeholder="Mother's Occupation" required />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{`Guardian's`} Occupation</td>
                                        <td><input name="guardian_occupation" id="guardianOccupation" type="text" placeholder="Guardian's Occupation (Optional)" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Alternative Number</td>
                                        <td><input name="alternative_number" id="alternativeNumber" type="text" placeholder="Alternative Number" /></td>
                                    </tr>
                                    <tr>
                                        <td>Father/Guardian Mobile No. <span className="required">(*)</span>
                                        </td>
                                        <td><input name="father_guardian_mobile" id="fatherGuardianMobile" type="text" placeholder="Mobile Number" required /></td>
                                    </tr>
                                    <tr>
                                        <td>Country <span className="required">(*)</span></td>
                                        <td>
                                            <div className="form-select-option">
                                                <NiceSelect
                                                    options={countryItems}
                                                    defaultCurrent={0}
                                                    onChange={selectHandler}
                                                    filterIcon={false}
                                                    name=""
                                                    className="course-orderby"
                                                />
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Religion <span className="required">(*)</span></td>
                                        <td>
                                            <div className="form-select-option">
                                                <NiceSelect
                                                    options={religionItems}
                                                    defaultCurrent={0}
                                                    onChange={selectHandler}
                                                    filterIcon={false}
                                                    name=""
                                                    className="course-orderby"
                                                />
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Blood Group <span className="required">(*)</span></td>
                                        <td>
                                            <div className="form-select-option">
                                                <NiceSelect
                                                    options={bloodGroup}
                                                    defaultCurrent={0}
                                                    onChange={selectHandler}
                                                    filterIcon={false}
                                                    name=""
                                                    className="course-orderby"
                                                />
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Birth Certificate No. <span className="required">(*)</span></td>
                                        <td><input name="birth_certificate_no" id="birthCertificateNo" type="text" placeholder="Birth Certificate No." /></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div className="bd-form-divider"></div>
                    <div className="row gy-30 align-items-center">
                        {/* <!-- Picture Upload Section --> */}
                        <div className="col-lg-4 col-md-7">
                            <div className="form-input-box">
                                <div className="form-input-title">
                                    <label htmlFor="picture" data-error="wrong" data-success="right">Upload
                                        Picture</label>
                                </div>
                                <div className="form-input mb-10">
                                    <input name="name" id="picture" type="file" placeholder="Passing Year" />
                                </div>
                                <em>Single dot (.) allowed in the picture name. Supported formats:
                                    .webp,
                                    .webp, .webp, .gif (lowercase only).</em>
                            </div>
                        </div>

                        {/* <!-- Picture Display Section --> */}
                        <div className="col-lg-2 col-md-5">
                            <div className="applicant-photo-preview">
                                <Image src={bgPrevImg} style={{ width: 'auto', height: 'auto' }} alt="image" />
                            </div>
                        </div>

                        {/* <!-- Signature Upload Section --> */}
                        <div className="col-lg-3 col-md-7">
                            <div className="form-input-box">
                                <div className="form-input-title">
                                    <label htmlFor="picture" data-error="wrong" data-success="right">Signature</label>
                                </div>
                                <div className="form-input mb-10">
                                    <input name="name" id="picture" type="file" placeholder="Passing Year" />
                                </div>
                                <em>Single dot (.) allowed in the file name. Supported formats: .webp,
                                    .webp, .webp, .gif (lowercase only).</em>
                            </div>
                        </div>

                        {/* <!-- Signature Display Section --> */}
                        <div className="col-lg-3 col-md-5">
                            <div className="applicant-signature-preview">
                                <Image src={bgSignatureImg} style={{ width: 'auto', height: 'auto' }} alt="image" />
                            </div>
                        </div>
                    </div>
                    <div className="bd-form-divider"></div>
                    <div className="row">
                        <div className="col-12">
                            <div className="bd-apply-declaration">
                                <h3 className="bd-course-details-content-title">Declaration</h3>
                                <div className="checkout-option">
                                    <input id="read_all" type="checkbox" required />
                                    <label htmlFor="read_all">I hereby declare that I have read and
                                        understood the terms and conditions of the website, and I agree
                                        to abide by them.</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="row justify-content-center">
                        <div className="col-xl-5">
                            <div className="bd-apply-submit-btn mt-50">
                                <button className="bd-btn btn-outline-primary w-100" type="submit">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </>
    );
};

export default ApplicationForm;