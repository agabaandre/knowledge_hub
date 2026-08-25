import AcademicCalendarMain from '@/components/pages/page-layout-two/academic-calendar/AcademicCalendarMain';
import Wrapper from '@/layout/DefaultWrapper';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Academic Calendar - Education & Online Courses React NextJs Template",
};

const AcademicCalendar = () => {
    return (
        <>
            <Wrapper>
                <main>
                    <AcademicCalendarMain />
                </main>
            </Wrapper>
        </>
    );
};

export default AcademicCalendar;