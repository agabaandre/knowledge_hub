<?php

namespace App\Support;

/**
 * Curated Africa public-health facts used when OpenAI is unavailable or returns too few items.
 */
final class AfricaHealthFactsFallback
{
    /**
     * @return list<array{title: string, summary: string, description: string}>
     */
    public static function facts(): array
    {
        return [
            [
                'title' => 'Immunization remains a backbone of child survival',
                'summary' => 'Routine vaccination programmes across the African region prevent millions of cases of measles, polio, and other vaccine-preventable diseases each year.',
                'description' => 'National immunization programmes, often supported by Gavi and partners, have dramatically reduced childhood deaths. Maintaining high coverage and catch-up campaigns after disruptions is essential for outbreak prevention and for meeting regional elimination goals.',
            ],
            [
                'title' => 'Malaria still drives a major share of outpatient visits',
                'summary' => 'Malaria continues to place heavy demand on primary care, especially for children under five in high-transmission settings.',
                'description' => 'Vector control (insecticide-treated nets, indoor residual spraying), seasonal chemoprevention where appropriate, prompt diagnosis, and effective treatment remain pillars of response. Research on resistance and new tools is critical as parasites and vectors evolve.',
            ],
            [
                'title' => 'Maternal health improves when skilled care is accessible',
                'summary' => 'Deliveries with skilled birth attendants and timely emergency obstetric care reduce maternal and newborn mortality.',
                'description' => 'Investments in antenatal care, facility readiness, referral networks, and respectful maternity care help close gaps between urban and rural populations. Family planning and adolescent-friendly services further support outcomes.',
            ],
            [
                'title' => 'HIV programmes show what sustained financing can achieve',
                'summary' => 'Scale-up of testing, antiretroviral therapy, and prevention has transformed HIV from an acute mortality crisis into a manageable chronic condition for many.',
                'description' => 'Retention in care, viral load monitoring, prevention for key populations, and addressing stigma remain priorities. Integration with primary health care and community systems strengthens long-term sustainability.',
            ],
            [
                'title' => 'TB requires both treatment and prevention',
                'summary' => 'Tuberculosis control depends on finding cases early, completing therapy, and addressing latent infection in high-risk groups.',
                'description' => 'Drug-resistant TB poses a growing challenge that demands laboratory capacity, infection prevention in facilities, and patient-centred support to finish long regimens. Social determinants such as nutrition and overcrowding influence transmission.',
            ],
            [
                'title' => 'Non-communicable diseases are a rising share of the burden',
                'summary' => 'Hypertension, diabetes, cancers, and mental health conditions increasingly shape hospital admissions and premature adult deaths.',
                'description' => 'Screening at primary level, affordable medicines, tobacco control, healthy diets, and physical activity promotion are cost-effective strategies. Many countries are integrating NCD services into universal health coverage reforms.',
            ],
            [
                'title' => 'Outbreak readiness saves time when hours matter',
                'summary' => 'Preparedness—surveillance, laboratory networks, trained rapid-response teams, and clear coordination—compresses the interval from detection to control.',
                'description' => 'Recent experience with Ebola, cholera, mpox, and COVID-19 underscored the value of regional collaboration, transparent communication, and logistics for vaccines and supplies. Simulation exercises and after-action reviews strengthen the next response.',
            ],
            [
                'title' => 'Safe water and sanitation underpin disease prevention',
                'summary' => 'Cholera, typhoid, and many diarrhoeal diseases decline when communities gain reliable WASH services and hygiene behaviour support.',
                'description' => 'Climate shocks and urbanisation strain water systems. Cross-sector coordination between health, water utilities, and local government is needed to sustain gains and to target hotspots during outbreaks.',
            ],
            [
                'title' => 'Nutrition links to immunity, growth, and NCD risk',
                'summary' => 'Stunting, wasting, micronutrient deficiencies, and rising overweight coexist in parts of the region, each with distinct policy responses.',
                'description' => 'Exclusive breastfeeding, complementary feeding, food fortification, school meals, and sugar-sweetened beverage policies are evidence-based levers. Agriculture and social protection programmes amplify nutrition outcomes.',
            ],
            [
                'title' => 'Health workforce density still lags demand',
                'summary' => 'There are not enough doctors, nurses, midwives, and community health workers relative to population need, especially in rural areas.',
                'description' => 'Training scale-up, retention incentives, task shifting where appropriate, and digital tools for supervision can extend coverage. Ethical international recruitment practices protect both source and destination countries.',
            ],
            [
                'title' => 'Primary health care is the most inclusive platform for UHC',
                'summary' => 'When PHC teams deliver prevention, early treatment, and referral, households face fewer catastrophic costs and better continuity of care.',
                'description' => 'Africa’s New Public Health Order and UHC roadmaps emphasise community engagement, essential service packages, and public financing reforms. Quality measurement keeps expansion accountable.',
            ],
            [
                'title' => 'Climate change amplifies health risks',
                'summary' => 'Heat stress, shifting vector ranges, flood-related displacement, and crop failures interact with infectious disease and mental health.',
                'description' => 'Adaptation includes heat-health action plans, climate-informed surveillance, resilient infrastructure, and One Health approaches at the human–animal–environment interface. Mitigation through cleaner energy also reduces air pollution deaths.',
            ],
            [
                'title' => 'Antimicrobial resistance threatens modern medicine',
                'summary' => 'Misuse of antibiotics in humans and agriculture accelerates resistance, complicating treatment of common infections.',
                'description' => 'Stewardship programmes, infection prevention, laboratory-guided therapy, and regulation of pharmacy sales are essential. Regional collaboration on surveillance helps track resistant strains.',
            ],
            [
                'title' => 'Road injuries are a leading cause of youth mortality',
                'summary' => 'Speed control, helmet and seat-belt use, safer road design, and post-crash care prevent deaths and disabilities.',
                'description' => 'Many countries are strengthening traffic law enforcement and emergency medical services. Data systems that capture injury patterns guide where to invest first.',
            ],
            [
                'title' => 'Mental health belongs in every health conversation',
                'summary' => 'Depression, anxiety, substance use, and psychosis contribute to disability but are under-diagnosed and under-treated.',
                'description' => 'Integrating brief psychological interventions into PHC, training non-specialists, and reducing stigma improve access. Youth mental health is a growing priority linked to unemployment and conflict stressors.',
            ],
            [
                'title' => 'Cervical cancer can be prevented and treated early',
                'summary' => 'HPV vaccination for girls, screening, and timely treatment of precancerous lesions sharply reduce mortality.',
                'description' => 'Where cytology capacity is limited, visual inspection with acetic acid or HPV DNA testing offers pragmatic pathways. Survivor support and palliative care remain important where disease presents late.',
            ],
            [
                'title' => 'Digital health tools extend reach when governed well',
                'summary' => 'Telemedicine, electronic registries, and SMS reminders can improve follow-up if privacy, connectivity, and equity are addressed.',
                'description' => 'Interoperability standards, cybersecurity, and human-centred design distinguish pilots that scale from those that stall. Community health workers with mobile tools have shown impact on maternal and child outcomes.',
            ],
            [
                'title' => 'Regional institutions accelerate joint procurement and learning',
                'summary' => 'Coordinated purchasing and knowledge sharing lower costs for vaccines, diagnostics, and therapeutics.',
                'description' => 'Africa CDC and RECs play growing roles in surveillance, workforce development, and emergency operations. Harmonised regulation speeds approval of quality-assured products.',
            ],
            [
                'title' => 'Community health workers bridge facilities and households',
                'summary' => 'Trusted CHWs improve uptake of services, adherence, and early detection when supervised and supplied.',
                'description' => 'Formalising CHW programmes with stipends, training ladders, and digital job aids improves retention. They were indispensable during COVID-19 vaccination and outbreak response.',
            ],
            [
                'title' => 'Research and local manufacturing strengthen sovereignty',
                'summary' => 'Domestic clinical trials, vaccine manufacturing partnerships, and regulatory maturity reduce dependence during crises.',
                'description' => 'Continental consultations on R&D prioritise diseases that affect Africans most. Technology transfer and good manufacturing practice build the ecosystem for vaccines and essential medicines.',
            ],
            [
                'title' => 'Health financing reforms determine who gets care',
                'summary' => 'Pooling funds through insurance or tax financing protects households better than out-of-pocket payment at the point of care.',
                'description' => 'Strategic purchasing, elimination of user fees for priority services, and efficiency gains in hospitals free resources for PHC. External aid should align with national plans and strengthen systems, not parallel ones.',
            ],
            [
                'title' => 'Gender norms shape exposure and access to care',
                'summary' => 'Women and girls often face barriers to sexual and reproductive services; men may present late for HIV and NCD care.',
                'description' => 'Gender-transformative programmes, safe reporting pathways for violence, and male engagement strategies improve equity. Disaggregated data reveal where interventions should focus.',
            ],
            [
                'title' => 'Conflict and displacement strain health systems',
                'summary' => 'Attacks on facilities, population movement, and broken supply chains increase mortality from both violence and preventable disease.',
                'description' => 'Humanitarian corridors, trauma surgery capacity, epidemic readiness in camps, and mental health support are life-saving. Rebuilding after conflict requires long-term investment in infrastructure and trust.',
            ],
            [
                'title' => 'Tobacco and alcohol control reduce future NCD waves',
                'summary' => 'Taxation, advertising bans, and smoke-free spaces are among the most cost-effective policies available.',
                'description' => 'Industry marketing increasingly targets young people online. Strong enforcement of existing laws and plain packaging complement price measures.',
            ],
            [
                'title' => 'Laboratory networks are the eyes of public health',
                'summary' => 'Quality-assured diagnostics for HIV viral load, TB, malaria, and emerging pathogens guide treatment and outbreak decisions.',
                'description' => 'Sample transport, biosafety, equipment maintenance, and external quality assurance programmes must be budgeted—not treated as optional. Genomic surveillance is expanding for influenza, SARS-CoV-2, and other threats.',
            ],
        ];
    }
}
