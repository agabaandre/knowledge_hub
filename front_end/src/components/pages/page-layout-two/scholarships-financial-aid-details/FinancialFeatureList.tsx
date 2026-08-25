interface FinancialFeature {
  title: string;
  content: string[];
}

const financialFeatures: FinancialFeature[] = [
  {
    title: "Eligibility Criteria",
    content: [
      "All applicants must be enrolled as full-time undergraduate students at the University.",
      "Students must maintain a minimum GPA of 3.0 to qualify for merit-based scholarships.",
      "Financial aid will be granted based on demonstrated financial need, as verified through relevant documentation."
    ]
  },
  {
    title: "Application Process",
    content: [
      "Students must apply for financial aid or scholarships by the deadlines set by the University each semester.",
      "Incomplete or late applications will not be considered.",
      "Renewal of financial aid or scholarships must be requested each academic year, with updated financial documents submitted where applicable."
    ]
  },
  {
    title: "Scholarship & Aid Conditions",
    content: [
      "Scholarships and financial aid are non-transferable and can only be applied toward tuition fees.",
      "If a student qualifies for more than one scholarship or discount, they will be eligible to receive only the highest award.",
      "Recipients of financial aid or scholarships are required to maintain full-time enrollment status (minimum of 12 credit hours per semester) and a cumulative GPA of 2.5 or above (unless otherwise specified)."
    ]
  },
  {
    title: "Merit-Based Scholarships",
    content: [
      "Merit scholarships are awarded based on academic performance, with the top students from each department receiving recognition.",
      "The scholarship percentage may vary from 25% to 100%, depending on the student's GPA and departmental ranking.",
      "Students losing eligibility due to failure to maintain academic standards may appeal but will be subject to review."
    ]
  },
  {
    title: "Need-Based Financial Aid",
    content: [
      "Financial aid is awarded after an evaluation of the family’s financial standing, which requires submission of income statements and other supporting documents.",
      "Aid amounts will depend on the demonstrated need and available funds. The maximum aid granted will cover up to 50% of the tuition fees.",
      "Financial aid recipients may be required to participate in community service or University programs as a condition for continued assistance."
    ]
  },
  {
    title: "Tuition Fee Discounts",
    content: [
      "Discounts apply to students who meet certain conditions (e.g., sibling discounts, children of University employees, alumni benefits).",
      "These discounts cannot be combined with other scholarships or financial aid and will be awarded based on the highest eligible amount.",
      "In case of sibling enrollment, the discount applies to both siblings as long as they remain enrolled together."
    ]
  },
  {
    title: "Disbursement",
    content: [
      "All financial aid and scholarship funds are directly applied to the student’s tuition account. No cash disbursements will be made.",
      "In case of withdrawal or deferral, the financial aid/scholarship will be forfeited, unless an exception is approved by the University administration."
    ]
  },
  {
    title: "Renewal & Review",
    content: [
      "Financial assistance will be reviewed at the end of each academic year, and students must meet the specified criteria for continuation.",
      "Changes in financial circumstances must be reported immediately to the financial aid office for re-evaluation.",
      "The University reserves the right to modify or terminate financial assistance in case of violations of the University’s academic or conduct policies."
    ]
  },
  {
    title: "Termination of Financial Aid",
    content: [
      "Financial assistance may be terminated if the student fails to meet academic or conduct standards, provides false information during the application process, or violates University policies.",
      "Termination of aid may also occur if the student does not meet full-time enrollment or GPA requirements, or if there is a significant change in their financial circumstances."
    ]
  },
  {
    title: "Appeals",
    content: [
      "Students who lose financial aid eligibility may submit an appeal for reconsideration, providing relevant documentation and explanations.",
      "Appeals must be submitted within 30 days of receiving notice of aid termination.",
      "The appeals committee’s decision will be final and binding."
    ]
  }
];

const FinancialFeatureBox: React.FC<{ index: number; title: string; content: string[] }> = ({ index, title, content }) => (
  <div className="bd-financial-feature-box">
    <h3 className="bd-details-content-title">
      {index + 1}. {title}
    </h3>
    <div className="bd-financial-feature-list">
      <ul>
        {content.map((item, i) => (
          <li key={i}>{item}</li>
        ))}
      </ul>
    </div>
  </div>
);

const FinancialFeaturesList: React.FC = () => (
  <div>
    {financialFeatures.map((feature, index) => (
      <FinancialFeatureBox
        key={index}
        index={index}
        title={feature.title}
        content={feature.content}
      />
    ))}
  </div>
);

export default FinancialFeaturesList;
