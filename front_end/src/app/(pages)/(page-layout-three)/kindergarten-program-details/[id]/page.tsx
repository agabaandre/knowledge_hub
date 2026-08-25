import KindergartenProgramDetailsMain from '@/components/pages/page-layout-three/kindergartent-program-details/KindergartenProgramDetailsMain';
import Wrapper from '@/layout/DefaultWrapper';
import { Metadata } from 'next';
import React from 'react';
interface PageProps {
    params: Promise<{ id: number }>;
}

export const metadata: Metadata = {
    title: "KG Program Details - Education & Online Courses React NextJs Template",
};


const KindergartenProgramDetails = async (props: PageProps) => {
    const params = await props.params;
    const { id } = params;

    return (
        <>
            <Wrapper>
                <main>
                    <KindergartenProgramDetailsMain id={id} />
                </main>
            </Wrapper>
        </>
    );
};

export default KindergartenProgramDetails;