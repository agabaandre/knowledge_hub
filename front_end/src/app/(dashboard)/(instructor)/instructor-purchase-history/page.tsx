import InstructorPurchaseMain from '@/components/dashboard/instructor/instructor-purchase-history/InstructorPurchaseMain';
import Wrapper from '@/layout/DefaultWrapper';
import InstructorDashboardLayout from '@/layout/InstructorDashboardLayout';
import { Metadata } from 'next';
import React from 'react';

export const metadata: Metadata = {
    title: "Instructor Purchase History - Education & Online Courses React NextJs Template",
};

const InstructorPurchase = () => {
    return (
        <>
            <Wrapper>
                <main>
                    <InstructorDashboardLayout>
                    <InstructorPurchaseMain />
                    </InstructorDashboardLayout>
                </main>
            </Wrapper>
        </>
    );
};

export default InstructorPurchase;