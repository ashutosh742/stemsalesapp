-- ============================================================================
-- STEM CRM - Migration 036 - Seed: faq_entry (100 rows)
-- Categories: product (40), pricing (15), competitor (10),
--             policy_process (15), tech_crm (10), regional_govt (10)
-- ============================================================================
-- Plain English. No em-dashes. No non-ASCII.
-- Idempotent: INSERT IGNORE on primary key.
-- All rows: created_by_uid=1 (admin), status='published', published_at=NOW()
-- source_artifact_id=NULL (will be updated by app when artifacts are linked)
-- Author: STEM ops
-- Date: 2026-05-18
-- ============================================================================

INSERT IGNORE INTO faq_entry
  (question, answer, category, tags, source_artifact_id,
   created_by_uid, status, published_at)
VALUES

-- ============================================================================
-- PRODUCT (40 rows)
-- ============================================================================

('What named-lab configurations does STEM Learning offer?',
 'STEM Learning offers five named-lab configurations: AI Lab, Robotics Lab, Coding Lab, STEM Discovery Lab, and the Hybrid Smart Lab. Each configuration comes with a defined set of hardware kits, curriculum modules, teacher training sessions, and an AMC plan. The exact SKU details and hardware bill of materials are in the Q1 FY27 product brochure.',
 'product', 'named-lab,configuration,AI lab,robotics,coding', NULL, 1, 'published', NOW()),

('How many students can a standard Robotics Lab serve simultaneously?',
 'A standard Robotics Lab is designed for a batch size of 30 students per session, with 15 dual-station kit sets. Schools with larger class sizes can opt for the Extended Robotics Lab configuration (20 dual-station sets, batch size 40). Both options include a teacher console and projector setup.',
 'product', 'robotics,batch size,students,capacity', NULL, 1, 'published', NOW()),

('What is the minimum space requirement for an AI Lab?',
 'The AI Lab requires a minimum floor area of 600 sq ft with a ceiling height of at least 10 feet. The room must have at least 6 power sockets (15A rated), a stable internet connection of 10 Mbps or above, and air conditioning if the ambient temperature exceeds 32 degrees Celsius. STEM provides a site readiness checklist before installation.',
 'product', 'AI lab,space,installation,site readiness', NULL, 1, 'published', NOW()),

('What curriculum standards does STEM Learning align with?',
 'STEM Learning curricula are aligned with NEP 2020 competency frameworks, CBSE skill education guidelines, and NCERT activity-based learning principles. For government schools, the content also maps to Samagra Shiksha STEM objectives and PM SHRI school development benchmarks. International alignment with ISTE standards is available on request for IB and IGCSE schools.',
 'product', 'curriculum,NEP 2020,CBSE,NCERT,Samagra Shiksha', NULL, 1, 'published', NOW()),

('Does STEM Learning provide teacher training as part of the lab package?',
 'Yes. Every named-lab package includes a mandatory teacher training program: a 3-day residential orientation at the STEM Training Academy (Pune or Bangalore) for the designated STEM teacher, plus 2 refresher online sessions per academic year. Schools with 3 or more STEM teachers can request an on-site training sprint at an additional cost.',
 'product', 'teacher training,training,orientation', NULL, 1, 'published', NOW()),

('What is the warranty period on lab hardware kits?',
 'All hardware kits carry a 1-year on-site warranty from the date of installation sign-off. The warranty covers manufacturing defects and component failure under normal classroom use. Physical damage, water ingress, and voltage-surge damage are excluded. Extended warranty up to 3 years is available as an add-on at the time of contract signing.',
 'product', 'warranty,hardware,support', NULL, 1, 'published', NOW()),

('What is the AMC structure for STEM labs?',
 'The Annual Maintenance Contract (AMC) is priced at 10 to 15 percent of the hardware value per year, depending on the lab type and cluster location. AMC covers: 4 scheduled preventive maintenance visits per year, unlimited remote support calls, next-business-day replacement of consumables, and annual curriculum content updates. AMC begins after the warranty period expires unless the school opts for AMC-from-day-one at a discounted rate.',
 'product', 'AMC,maintenance,support,annual', NULL, 1, 'published', NOW()),

('What is the typical AMC structure for PMSHRI schools?',
 'For PMSHRI-designated schools, STEM Learning offers a government-aligned AMC: the AMC rate is fixed at 10 percent of hardware value, invoiced annually in arrears after government fund release. PMSHRI schools also receive priority scheduling for maintenance visits and a dedicated government-account helpline. Proof of PMSHRI sanction letter is required before contract execution.',
 'product', 'AMC,PMSHRI,government,maintenance', NULL, 1, 'published', NOW()),

('Can a school purchase only the curriculum without the hardware kits?',
 'Yes, STEM Learning offers a Curriculum-only subscription plan for schools that already have compatible hardware (laptops, tablets, sensors). The annual subscription covers access to the STEM digital content portal, teacher lesson plans, assessment rubrics, and bi-annual curriculum updates. Hardware procurement support is not included in this plan.',
 'product', 'curriculum,subscription,digital,hardware-free', NULL, 1, 'published', NOW()),

('What programming languages are covered in the Coding Lab?',
 'The Coding Lab curriculum covers Scratch (Grades 3 to 5), Python basics (Grades 6 to 8), HTML and CSS web fundamentals (Grade 7 to 8), and an optional advanced track covering Arduino C and basic data science with Python (Grades 9 to 12). All content is mapped to grade-level progression and includes unplugged activities for schools with intermittent internet access.',
 'product', 'coding lab,programming,Python,Scratch,Arduino', NULL, 1, 'published', NOW()),

('Does the Hybrid Smart Lab work without a permanent internet connection?',
 'Yes. The Hybrid Smart Lab includes a local content server (STEM Edge Box) that stores the full curriculum offline. Students can work on activities, submit assessments, and earn badges without live internet. The Edge Box syncs to the cloud when connectivity is available, typically overnight via the school WiFi or a 4G SIM inserted in the box.',
 'product', 'hybrid lab,offline,Edge Box,internet', NULL, 1, 'published', NOW()),

('How long does a full named-lab installation take from contract signing?',
 'Typical installation lead time is 6 to 8 weeks from contract signing: 2 weeks for site readiness verification and civil works (if required), 2 weeks for hardware procurement and dispatch, and 1 to 2 weeks for installation, calibration, and teacher onboarding on-site. Express installation (4 weeks) is available at a surcharge for schools in Tier 1 cities.',
 'product', 'installation,lead time,setup,timeline', NULL, 1, 'published', NOW()),

('What is the STEM Discovery Lab and who is it suited for?',
 'The STEM Discovery Lab is an entry-level, low-cost lab designed for primary and upper-primary schools (Grades 1 to 8) with limited budgets. It uses low-cost physical kits (bridge-building, simple machines, basic electricity) alongside a tablet-based digital workbook. It does not require a dedicated room and can be set up in a regular classroom. Ideal for government schools with Samagra Shiksha or CSR funding constraints below Rs 8 lakh.',
 'product', 'Discovery Lab,primary,entry level,government,budget', NULL, 1, 'published', NOW()),

('Can a school upgrade from a Discovery Lab to a full AI Lab?',
 'Yes, STEM Learning supports a phased upgrade path. A school that starts with the Discovery Lab can upgrade to any named-lab configuration within 3 years. The trade-in value of the Discovery Lab hardware (typically 30 to 40 percent of original cost) is credited against the new lab purchase. The BD must tag the upgrade intent in the CRM for the RM to track.',
 'product', 'upgrade,Discovery Lab,AI Lab,phased', NULL, 1, 'published', NOW()),

('What assessments are built into the STEM curriculum?',
 'Each module includes: pre-assessment (diagnostic), 3 formative checkpoint quizzes, one project-based assessment, and a post-assessment. For Grades 9 to 12, there is an optional external certification exam run by STEM Learning in partnership with NSQF-aligned assessment bodies. Schools receive a termly report card per student with skill-wise scores that can be shared with parents.',
 'product', 'assessment,quiz,certification,NSQF', NULL, 1, 'published', NOW()),

('What is the student-to-kit ratio in a Robotics Lab?',
 'The standard ratio is 2 students per kit set. Each kit set contains a microcontroller board, sensor pack, motor driver, and a set of structural components. For competition-track schools (aiming at robotics olympiads), a 1 student per kit option is available at a 40 percent hardware cost premium.',
 'product', 'robotics,student ratio,kit,competition', NULL, 1, 'published', NOW()),

('Does STEM Learning provide content in regional languages?',
 'STEM Learning currently provides teacher guides and student workbooks in English and Hindi for all lab types. Tamil, Marathi, and Bengali translations are available for the Discovery Lab curriculum. Regional content for remaining states is in progress and expected by Q2 FY27. Schools in non-Hindi non-English dominant regions can request bilingual instruction support during teacher training.',
 'product', 'regional language,Hindi,Tamil,Bengali,bilingual', NULL, 1, 'published', NOW()),

('What is the shelf life of the hardware kits?',
 'STEM hardware kits are designed for a minimum 5-year classroom lifespan under normal use. Consumables (wiring, LEDs, small sensors) are replenished annually under AMC. Structural plastic components have been stress-tested for 3,000 assembly-disassembly cycles. Microcontroller boards carry a 2-year replacement guarantee against manufacturing defects.',
 'product', 'hardware,lifespan,durability,shelf life', NULL, 1, 'published', NOW()),

('Can the lab run without a dedicated STEM teacher?',
 'STEM strongly recommends a dedicated in-house STEM teacher who has completed the 3-day training program. For schools that cannot designate a teacher initially, STEM offers a Lab Facilitator On-Demand service (remote expert joins via video call for 2 sessions per week for the first term). This service is charged separately and is not included in standard packages.',
 'product', 'teacher,facilitator,remote,dedicated', NULL, 1, 'published', NOW()),

('What data analytics does the principal get from the lab?',
 'The principal receives a monthly STEM Insights dashboard: class-wise student engagement rate, average score by competency, top 10 student performers, and curriculum completion percentage. The dashboard is accessible via web browser and as a PDF report emailed to the principal. Schools with 3 or more labs get cluster-level comparative data.',
 'product', 'analytics,dashboard,data,principal,insights', NULL, 1, 'published', NOW()),

('Is there a trial or pilot option before full lab purchase?',
 'STEM Learning offers a 60-day Lab Trial for qualifying schools: 1 kit set per lab type is installed for 2 months, including 3 teacher training sessions and curriculum access. Trial fee is Rs 35,000 (fully adjusted against purchase price if the school proceeds). The trial is available in Mumbai, Pune, Hyderabad, Chennai, and Bangalore clusters.',
 'product', 'trial,pilot,demo,60 day', NULL, 1, 'published', NOW()),

('What is the power consumption of a fully equipped Robotics Lab?',
 'A 30-station Robotics Lab draws approximately 3 to 4 kVA of peak power. Each station consumes around 80 to 100 watts (laptop, kit controller, and peripheral sensors). The STEM site readiness team checks the school electrical load before installation and recommends UPS sizing. Standard school power supply of 5 kVA is sufficient for the basic configuration.',
 'product', 'power,electrical,Robotics Lab,kVA,UPS', NULL, 1, 'published', NOW()),

('Can STEM labs be used for non-STEM subjects?',
 'The lab infrastructure is purpose-built for STEM disciplines. However, several schools use the Coding Lab for digital literacy and computational thinking sessions for non-STEM teachers. The Discovery Lab physical kits have been used by science and math teachers for demonstration experiments. STEM Learning encourages cross-subject use and provides supplementary activity packs on request.',
 'product', 'cross-subject,digital literacy,science,math', NULL, 1, 'published', NOW()),

('What happens if a school fails to pay the AMC invoice on time?',
 'If AMC payment is overdue by more than 30 days, remote support calls are paused. If overdue by more than 60 days, scheduled preventive visits are also paused. Hardware replacement continues only for defects that pre-date the overdue period. All services resume within 48 hours of payment clearance. The BD must flag AMC overdue accounts to the RM for collections follow-up.',
 'product', 'AMC,payment,overdue,collections', NULL, 1, 'published', NOW()),

('Does STEM Learning offer a lab for early childhood (Grade 1 to 2)?',
 'Yes, the Tinker Lab is a new offering (pilot phase) designed for Grades 1 to 3. It uses unplugged and screen-free activities: building blocks, sorting puzzles, simple machines, and guided observation journals. No electronics hardware is involved. The Tinker Lab is priced below Rs 3 lakh for a full classroom set and does not require IT infrastructure.',
 'product', 'Tinker Lab,early childhood,Grade 1,Grade 2,unplugged', NULL, 1, 'published', NOW()),

('What is the content update frequency for the STEM curriculum?',
 'Core curriculum modules are updated annually (August release, aligned with the new academic year). Hot-topic elective modules (e.g., generative AI basics, drone programming) are released quarterly as add-ons. Schools on active AMC receive all updates at no additional charge. Schools not on AMC can purchase individual module updates at a flat rate.',
 'product', 'curriculum update,annual,quarterly,AMC', NULL, 1, 'published', NOW()),

('Can a school customise the lab curriculum for their board (ICSE, IB, State Board)?',
 'STEM Learning offers a Curriculum Customisation service for schools requiring board-specific alignment. For ICSE and CBSE schools, the standard curriculum maps to existing syllabi and minor adjustments are included at no cost. For IB MYP and IGCSE programmes, a paid customisation sprint (typically 4 to 6 weeks) is required to produce board-tagged lesson plans. State board alignments are available for Maharashtra, Tamil Nadu, Karnataka, and West Bengal.',
 'product', 'customisation,ICSE,IB,state board,alignment', NULL, 1, 'published', NOW()),

('What student competition programmes does STEM Learning run?',
 'STEM Learning organises the STEM Olympiad series: cluster-level robotics competitions (October), a national coding hackathon (January), and an AI Innovation Challenge (March). Schools enrolled in any named-lab programme participate at no additional fee. Top 3 school teams per cluster win hardware upgrade vouchers. National winners receive mentorship from the STEM R&D team.',
 'product', 'competition,Olympiad,robotics,hackathon,AI challenge', NULL, 1, 'published', NOW()),

('How does STEM Learning handle lab furniture and seating?',
 'Standard lab furniture (workstations, stools, cable management trays, and storage racks) is included in the full named-lab packages (AI Lab, Robotics Lab, Coding Lab). The Discovery Lab and Tinker Lab use existing classroom furniture and do not include dedicated lab furniture. Custom furniture for special-needs accessibility is available as an add-on.',
 'product', 'furniture,workstation,seating,setup', NULL, 1, 'published', NOW()),

('Is there a parent-facing report or communication from the STEM lab?',
 'Schools can opt into the Parent Connect feature: a termly PDF report per student emailed to the parent email on file. The report shows the student skill badges earned, project scores, and teacher comment. The school principal must enable this feature in the STEM admin portal. STEM does not contact parents directly without school consent.',
 'product', 'parent,report,communication,badge', NULL, 1, 'published', NOW()),

('What safety certifications do the hardware kits carry?',
 'All STEM hardware kits are CE and RoHS certified. Kits used in Grades 1 to 5 carry an additional Child Safety EN71 certification. All electronics components are rated for safe classroom use (max 12V DC). STEM provides Material Safety Data Sheets for any chemical components in discovery experiment packs. The installation team conducts a safety orientation with the school administration on day of setup.',
 'product', 'safety,certification,CE,RoHS,EN71', NULL, 1, 'published', NOW()),

('Does STEM Learning have a reseller or franchise model?',
 'STEM Learning operates a Cluster Partner programme for educational service providers who want to distribute STEM labs in Tier 2 and Tier 3 cities. Partners receive a 10 to 15 percent margin on hardware sales and a 5 percent share of AMC revenue. Onboarding requires a Rs 5 lakh refundable deposit and completion of the 2-day Partner Certification workshop. Contact the BD Head for referrals.',
 'product', 'reseller,franchise,cluster partner,Tier 2,Tier 3', NULL, 1, 'published', NOW()),

('Can the Coding Lab be used without laptops if the school has tablets?',
 'Yes. The Coding Lab curriculum is compatible with Android tablets (10 inch or larger, Android 10 or above) running the STEM Learner app. The app works in both online and offline mode. Physical coding kits (Micro:bit or Arduino-based) that connect via Bluetooth are also tablet-compatible. STEM will provide a tablet compatibility checklist and test the specific tablet model before confirming support.',
 'product', 'Coding Lab,tablet,Android,STEM Learner app', NULL, 1, 'published', NOW()),

('How does STEM Learning support schools during examination periods?',
 'STEM Learning pauses scheduled maintenance visits during the school examination period (typically February to March and October for term exams) unless requested by the school. Remote support remains active. Curriculum sessions can be paused and resumed in the STEM teacher portal without data loss. The BD should update the school contact calendar to avoid scheduling conflicts during exam windows.',
 'product', 'examination,support,maintenance,pause', NULL, 1, 'published', NOW()),

('What is the process for reporting a hardware defect?',
 'Hardware defects should be reported via the STEM Support Portal (support.stemlearning.in) or by calling the dedicated school helpline (9AM to 6PM, Monday to Saturday). The support team raises a ticket within 2 hours and dispatches a technician within 2 business days for Tier 1 cities, 3 to 5 business days for Tier 2 and Tier 3 cities. Critical defects (lab fully non-operational) get same-day remote diagnosis and next-day on-site visit.',
 'product', 'hardware defect,support,helpline,repair,ticket', NULL, 1, 'published', NOW()),

('Can the lab be relocated to a different room or campus?',
 'Relocation within the same campus is supported at no cost under AMC, subject to the new room meeting site readiness requirements. Relocation to a different campus or city is treated as a new installation and is charged at the standard installation fee. The school must notify STEM Learning at least 30 days before planned relocation.',
 'product', 'relocation,installation,campus,AMC', NULL, 1, 'published', NOW()),

('Does STEM Learning offer any lab for vocational or skill-based courses (Class 9 to 12)?',
 'Yes. The STEM Skill Lab is designed for secondary and senior secondary students pursuing skill education under NSQF. It covers IT-ITES, Electronics Technician, and Data Entry Operator vocational tracks. The curriculum meets PSSCIVE and CBSE skill subject guidelines. Schools offering Class 11 and 12 skill electives can use the STEM Skill Lab as their practical infrastructure.',
 'product', 'vocational,Skill Lab,NSQF,Class 11,Class 12', NULL, 1, 'published', NOW()),

('What is the process for an end-of-contract handover or lab decommissioning?',
 'At the end of a contract period (typically 3 or 5 years), STEM Learning schedules a decommissioning audit: hardware is inventoried, consumables are logged, and the school is issued a return-condition certificate. If the school renews, a new contract is signed and hardware is refreshed or retained depending on condition. If the school does not renew, all hardware (except site-fixed civil works) is removed within 30 days.',
 'product', 'decommissioning,end of contract,renewal,hardware return', NULL, 1, 'published', NOW()),

('What digital content platform does STEM Learning use?',
 'STEM Learning uses its own LMS: STEM Learner Platform (SLP), accessible via web browser and the STEM Learner Android app. Teachers manage lesson plans, assign activities, grade submissions, and generate reports. Students log in with their school-issued ID. SLP integrates with Google Workspace for Education and Microsoft 365 for schools on those platforms. API access is available for schools with third-party SIS systems.',
 'product', 'LMS,STEM Learner Platform,SLP,digital,app', NULL, 1, 'published', NOW()),

('Does STEM Learning provide lab signage and branding?',
 'Yes. Each lab installation includes the standard STEM Learning lab branding package: a vinyl entry board with the school name and lab type, 6 motivational posters, kit storage labels, and a student achievement display board. Custom co-branding (school logo on entry board) is available at no extra charge. CSR-funded labs can display the sponsor company logo as per CSR donor guidelines.',
 'product', 'branding,signage,CSR,sponsor,co-branding', NULL, 1, 'published', NOW()),

('What is the content coverage for an AI Lab curriculum?',
 'The AI Lab curriculum covers: AI fundamentals (what is AI, supervised vs unsupervised learning), image recognition projects using camera modules, natural language processing basics (chatbot building), data collection and basic analysis using spreadsheets, and an ethics of AI module. Students build at least 3 end-to-end AI projects per academic year. The curriculum is updated annually to reflect industry advances.',
 'product', 'AI Lab,curriculum,image recognition,NLP,ethics', NULL, 1, 'published', NOW()),

-- ============================================================================
-- PRICING (15 rows)
-- ============================================================================

('What is the price range for a full Robotics Lab?',
 'A standard 30-station Robotics Lab is priced between Rs 12 lakh and Rs 18 lakh depending on hardware specification, city tier, and negotiated contract terms. This includes hardware, furniture, curriculum licence for 3 years, installation, and the first teacher training cohort. AMC is quoted separately at 10 to 15 percent of hardware value per year. Actual quote must be prepared using the current Q1 FY27 pricing sheet; BDs must not share indicative prices without CM clearance.',
 'pricing', 'Robotics Lab,price,quote,Rs lakh', NULL, 1, 'published', NOW()),

('What is the pricing for an AI Lab?',
 'An AI Lab (30 stations, camera module, GPU server included) is priced between Rs 18 lakh and Rs 28 lakh. The wide range reflects variability in GPU server tier, connectivity hardware, and whether furniture is included. The standard package at Rs 22 lakh covers 20 stations with a shared inference server. BDs must use the Q1 FY27 pricing sheet and get the quote approved before sharing with the school.',
 'pricing', 'AI Lab,price,quote,GPU,Rs lakh', NULL, 1, 'published', NOW()),

('What is the typical pricing for a 200-school district deal?',
 'District-level deals (100 schools or more) are priced on a customised volume-discount structure negotiated directly by the Director and AVP. Typical indicative range: Rs 6 to 10 lakh per school for the Discovery Lab tier, Rs 10 to 16 lakh per school for the Robotics or Coding Lab. A district of 200 schools could represent a Rs 120 to 200 crore total contract. BDs should flag any district lead to the RM immediately for Director-level engagement.',
 'pricing', 'district,200 schools,volume,bulk,price', NULL, 1, 'published', NOW()),

('Can a school pay in instalments?',
 'Yes. STEM Learning offers a standard 30-60-10 payment schedule: 30 percent on contract signing, 60 percent on installation sign-off, and 10 percent after 90-day post-installation review. For PMSHRI and government schools, the payment schedule is aligned with government fund release cycles: typically 50 percent on work order and 50 percent after physical verification. Custom payment plans require RM approval.',
 'pricing', 'instalments,payment schedule,government,PMSHRI', NULL, 1, 'published', NOW()),

('What discount can a BD offer at the negotiation stage?',
 'BDs are not authorised to offer any discount independently. The approved discount matrix (in the current pricing sheet) defines: up to 5 percent for multi-year contracts (3 years or more), up to 8 percent for multi-lab (3 or more labs at the same school), and up to 10 percent for district deals. Any discount beyond the matrix requires CM approval in writing before it is communicated to the school. Never quote a price without checking the live pricing sheet.',
 'pricing', 'discount,negotiation,matrix,approval', NULL, 1, 'published', NOW()),

('Is there a pricing difference between Tier 1 and Tier 2 cities?',
 'Yes. The standard pricing sheet includes a city-tier loading: Tier 1 cities (Mumbai, Delhi NCR, Bangalore, Hyderabad, Chennai, Pune) are priced at the base rate. Tier 2 cities attract a logistics surcharge of 3 to 5 percent. Tier 3 and rural locations may attract a further surcharge of 3 to 8 percent depending on distance from the nearest STEM service centre. The final logistics loading is calculated by the ops team at quoting stage.',
 'pricing', 'city tier,Tier 1,Tier 2,logistics surcharge', NULL, 1, 'published', NOW()),

('How is the AMC fee structured in the contract?',
 'AMC is contracted separately from the lab purchase. The standard AMC agreement is annual, auto-renewing unless the school gives 30 days written notice before renewal date. The AMC fee is indexed to 10 percent of the original hardware invoice value in year 1 and increases by 5 percent per year from year 2. Schools on 3-year pre-paid AMC get a 15 percent discount on the total AMC value.',
 'pricing', 'AMC,fee,structure,annual,pre-paid', NULL, 1, 'published', NOW()),

('What is the GST applicable on STEM lab purchases?',
 'Hardware components attract 18 percent GST. Curriculum and training services attract 18 percent GST. Installation services attract 18 percent GST. For government schools and schools registered as educational trusts under Section 12A, the GST input credit eligibility depends on the school accounting setup; BDs should recommend the school consult their accountant. STEM Learning issues a tax invoice with HSN codes for all line items.',
 'pricing', 'GST,tax,invoice,HSN,18 percent', NULL, 1, 'published', NOW()),

('Does STEM Learning offer a CSR-funded pricing model?',
 'For schools where a corporate CSR donor is funding the lab, STEM Learning can invoice the CSR donor directly or the school (with a tripartite agreement). CSR-funded labs do not attract a special discount by default, but CSR donors sponsoring 3 or more labs across a city receive a bundled-impact report that can be used in the donor annual report. BDs should loop in the CSR donor contact early and introduce them to the Director for larger deals.',
 'pricing', 'CSR,donor,funding,invoice,tripartite', NULL, 1, 'published', NOW()),

('What is the pricing for teacher training if purchased separately?',
 'Stand-alone teacher training (without a lab purchase) is priced at Rs 18,000 per teacher for the 3-day residential program or Rs 8,000 per teacher for the 1-day on-site sprint. Bulk training (10 or more teachers) gets a 20 percent group discount. Schools that have already purchased a lab and want to train additional teachers post-installation pay the stand-alone rate less 15 percent loyalty discount.',
 'pricing', 'teacher training,standalone,price,per teacher', NULL, 1, 'published', NOW()),

('How is curriculum renewal priced after the initial 3-year licence?',
 'After the 3-year curriculum licence period, renewal is priced at Rs 1,500 per student per year for a school of up to 300 students, and Rs 1,200 per student per year for schools above 300 students. Schools on active AMC receive a 20 percent curriculum renewal discount. The licence renewal includes all content updates released during the year.',
 'pricing', 'curriculum renewal,licence,per student,AMC discount', NULL, 1, 'published', NOW()),

('What is the refund policy if a school cancels after contract signing?',
 'Cancellation within 7 days of contract signing: full refund of any advance paid, less Rs 5,000 administration fee. Cancellation after 7 days but before installation begins: 80 percent refund of advance. Cancellation after installation begins: no refund; school may opt to pause the contract for up to 6 months with written notice. Hardware removal is charged to the school in cancellation-post-installation scenarios.',
 'pricing', 'refund,cancellation,policy,advance', NULL, 1, 'published', NOW()),

('What is the pricing for the Hybrid Smart Lab?',
 'The Hybrid Smart Lab (20 stations, Edge Box, offline curriculum) is priced between Rs 8 lakh and Rs 14 lakh depending on hardware specification. The offline Edge Box is included. Internet-dependent features (live competitions, cloud analytics) require a school broadband of at least 10 Mbps. The lab is designed for Tier 2, Tier 3, and rural schools with connectivity challenges.',
 'pricing', 'Hybrid Smart Lab,offline,Edge Box,price,rural', NULL, 1, 'published', NOW()),

('Can the school pay via bank EMI or education finance?',
 'STEM Learning has tie-ups with 2 NBFC partners for school equipment financing. The school pays a down payment of 20 percent and finances the remaining 80 percent over 24 or 36 months. Interest rates (currently 12 to 14 percent per annum) are the school bank account holders responsibility. STEM Learning does not bear the financing cost. The BD must introduce the NBFC contact to the school bursar and loop in the RM for approval.',
 'pricing', 'EMI,financing,NBFC,payment,down payment', NULL, 1, 'published', NOW()),

('What is the standard quote validity period?',
 'All STEM Learning quotes are valid for 30 days from the date of issue. After 30 days, the quote must be re-issued using the current pricing sheet, which may reflect updated hardware costs or logistics tariffs. BDs must not verbally extend quote validity without issuing an updated formal quote. This is a compliance requirement under the Proposal SLA (Migration 026).',
 'pricing', 'quote validity,30 days,pricing,compliance', NULL, 1, 'published', NOW()),

-- ============================================================================
-- COMPETITOR (10 rows)
-- ============================================================================

('How do we position against LabsOnline for an AI Lab deal?',
 'LabsOnline offers a software-only AI curriculum without physical hardware. STEM Learning differentiates on: (1) end-to-end delivery (hardware + curriculum + teacher training in one contract), (2) on-site support with a dedicated service engineer per cluster, and (3) NSQF-aligned certification that LabsOnline does not offer. If the school principal raises LabsOnline, ask whether they have already purchased hardware. If not, the all-in-one STEM package is a simpler procurement. If yes, offer the Curriculum-only subscription as a competitive alternative.',
 'competitor', 'LabsOnline,competitor,AI Lab,positioning', NULL, 1, 'published', NOW()),

('How do we handle the Lead School objection?',
 'Lead School (by Lead Edtech) offers an integrated school management platform with some STEM content modules as add-ons. Their STEM content is generic and not hands-on hardware based. Key talking points: (1) STEM Learning is specialised, not a feature inside a school ERP, (2) our teacher training is 3 days residential vs Lead School online videos, (3) competition programme (STEM Olympiad) gives students a tangible outcome that Lead School cannot match. Avoid naming Lead School first; let the principal raise them.',
 'competitor', 'Lead,Lead School,competitor,objection', NULL, 1, 'published', NOW()),

('What is the STEM counter to Vidyamandir Classes entering STEM labs?',
 'Vidyamandir Classes (VMC) has launched a co-branded STEM activity kit programme for Tier 1 coaching students. VMC targets JEE/NEET-aspirant schools, not primary-level STEM lab buyers. If a school principal mentions VMC: (1) confirm whether the school is a coaching feeder school or a regular school, (2) point out that STEM Learning serves the broader school (Grades 1 to 12) rather than a coaching overlay, (3) highlight that VMC does not offer a named lab, government scheme alignment, or AMC. VMC is not a direct competitor in the government-school or primary segment.',
 'competitor', 'Vidyamandir,VMC,competitor,positioning', NULL, 1, 'published', NOW()),

('What should a BD say if a school mentions Toppr School partnership?',
 'Toppr (now part of BYJU\'s School) offers a digital content subscription to schools. It is a tablet-based adaptive learning product, not a lab. Key differentiators: (1) STEM Learning provides physical lab infrastructure and hands-on learning, which Toppr does not, (2) STEM Learning is not under any regulatory scrutiny (unlike BYJU\'s group), (3) our AMC and on-site support model is not matched by Toppr. If the school is already on Toppr for academic subjects, STEM labs are complementary, not competing.',
 'competitor', 'Toppr,BYJU,competitor,positioning', NULL, 1, 'published', NOW()),

('A school says they can get a cheaper lab from a local vendor. How do we respond?',
 'Local vendors typically offer hardware kits without curriculum, teacher training, or post-installation support. Respond with: (1) ask the principal to compare the total cost of ownership (hardware + training + annual support) not just the initial kit price, (2) mention that STEM Learning\'s curriculum is NEP-aligned and includes a 3-year content licence, (3) offer to do a side-by-side comparison using the standard local vendor comparison card (available from the CM). Never disparage the vendor by name.',
 'competitor', 'local vendor,cheap,TCO,comparison', NULL, 1, 'published', NOW()),

('How should a BD respond when a school says they have a proposal from Tata Strive?',
 'Tata Strive focuses on vocational training for post-secondary students and is not a school STEM lab provider. If a school mentions Tata Strive, it is likely a different product category (IT skills or employability). Clarify the context: is the school looking for a vocational lab (Class 9 to 12) or a general STEM lab? For vocational Class 9 to 12, STEM Skill Lab competes directly; use the NSQF alignment and curriculum completeness as differentiators. Tata Strive does not offer hardware for school labs.',
 'competitor', 'Tata Strive,vocational,competitor,positioning', NULL, 1, 'published', NOW()),

('How do we compare against National Instruments LabVIEW-based solutions?',
 'National Instruments LabVIEW setups are used in engineering colleges and advanced high schools. They are significantly more expensive (Rs 5 to 8 lakh per station), require engineering-level teacher competence, and have no school-grade curriculum. STEM Learning targets the K-12 school market with purpose-built age-appropriate curricula. If an engineering-college feeder school raises NI, acknowledge the product for its engineering depth but position STEM as the pathway that prepares students to use NI-level tools later.',
 'competitor', 'National Instruments,LabVIEW,engineering,K-12', NULL, 1, 'published', NOW()),

('What is the STEM Learning response to Faber Castell STEM Lab competition?',
 'Faber Castell STEM offers craft-and-art-integrated STEM kits, primarily targeting the pre-primary and early primary segment. They do not offer a named lab, curriculum, or teacher training at the level STEM Learning does. For schools comparing the two: STEM Learning covers Grades 1 to 12 with a progressive curriculum; Faber Castell tops out around Grade 5 and does not include electronics or coding. The two can co-exist in the same school.',
 'competitor', 'Faber Castell,craft,pre-primary,positioning', NULL, 1, 'published', NOW()),

('How do we address the Maker\'s Asylum or Atal Tinkering Lab comparison?',
 'Atal Tinkering Labs (ATL) funded by NITI Aayog provide a government-sponsored STEM lab for Class 6 to 12 in government schools. Many government schools already have an ATL. STEM Learning is complementary to ATL: (1) ATL does not provide a structured curriculum or teacher training, (2) STEM Learning can operate alongside an ATL and helps the school make better use of the ATL hardware, (3) private schools cannot access ATL funding and are the primary STEM target market. Never position STEM as an alternative to ATL for government schools that are ATL grantees.',
 'competitor', 'Atal Tinkering Lab,ATL,NITI Aayog,government,complementary', NULL, 1, 'published', NOW()),

('A trust-run school says they are evaluating a local NGO\'s free STEM programme. How do we respond?',
 'NGO-run free STEM programmes are typically sporadic (once a week, volunteer-led) without a structured curriculum or continuity guarantee. STEM Learning offers: (1) a permanent named lab that the school owns, (2) a daily curriculum that the school teacher delivers, (3) accountability through assessments, reports, and AMC. Acknowledge the NGO positively and suggest the school could use both: the NGO for enrichment activities and STEM for the core lab programme. The principal will appreciate a non-confrontational approach.',
 'competitor', 'NGO,free,programme,positioning,trust school', NULL, 1, 'published', NOW()),

-- ============================================================================
-- POLICY / PROCESS (15 rows)
-- ============================================================================

('What is the barge meeting policy and what is the wallet deduction for barge meetings?',
 'A barge meeting is a joint school visit where the CM or RM accompanies the BD without prior notice to the school. Barge meetings are allowed only for leads in cstatus 8, 9, and 12 and must be pre-cleared by the RM in the daily rhythm system (Migration 035). Wallet deduction: if a CM calls a barge without RM pre-clearance and the school meeting is disrupted, a Rs 500 deduction is applied to the CM monthly incentive pool. BDs do not face deductions for barge meetings; only the CM who initiates an uncleared barge is liable.',
 'policy_process', 'barge meeting,wallet deduction,CM,cstatus 8,policy', NULL, 1, 'published', NOW()),

('What is the SLA for sending a proposal after a cstatus 6 (Positive) lead?',
 'Under Migration 026, a proposal must be sent within 14 calendar days of a lead moving to cstatus 6 (Positive). If the proposal is not sent within 14 days, a red flag is raised to the CM. If not sent within 21 days, the RM is alerted. The proposal must pass coach pre-review (Migration 036 asset review, grade B or above) before it is sent. The system logs proposal_sent_at on the init_call record.',
 'policy_process', 'SLA,proposal,cstatus 6,14 days,Migration 026', NULL, 1, 'published', NOW()),

('What is the daily plan submission deadline for BDs?',
 'BDs must submit their daily plan (next day\'s planned tasks) before 18:30 IST every weekday. Late submission (after 18:30 but before 20:00) is flagged as a yellow flag. Submission after 20:00 or no submission is a red flag. The plan submission gate also checks that all force-acknowledge knowledge artifacts are acknowledged before allowing submission (Migration 036 rule).',
 'policy_process', 'daily plan,18:30,deadline,submission,red flag', NULL, 1, 'published', NOW()),

('What is the MoM (Minutes of Meeting) submission rule?',
 'MoM must be submitted within 2 hours of the end of any school meeting at cstatus 3 or above. Late MoM is flagged in the daily rhythm red flag system. MoMs must include: attendees, meeting purpose, key points discussed, objections raised and response, agreed next action, and next meeting date. For joint meetings with the CM, the CM must countersign the MoM before it is considered complete.',
 'policy_process', 'MoM,minutes of meeting,2 hours,submission,CM', NULL, 1, 'published', NOW()),

('What is the travel advance policy for BDs?',
 'BDs can request a travel advance of up to Rs 5,000 per trip (outside home city) via the STEM Expense Portal. Advances must be requested at least 48 hours before travel. The advance is settled against receipts within 5 working days of return. Unreconciled advances older than 10 days result in the BD\'s next advance request being blocked. CM approval is required for any single trip advance above Rs 5,000.',
 'policy_process', 'travel advance,expense,Rs 5000,CM approval', NULL, 1, 'published', NOW()),

('What is the policy on sharing pricing or proposal documents with prospects on WhatsApp?',
 'BDs must not share price lists, discount grids, or draft proposals directly on personal WhatsApp without first passing the document through the STEM Secure Share tool (available in the CRM). Secure Share generates a time-limited link (72 hours) and logs the sharing event. BDs who share raw pricing files via personal WhatsApp are in breach of the pricing confidentiality policy and may face a formal warning on the first offence.',
 'policy_process', 'WhatsApp,pricing,proposal,sharing policy,secure share', NULL, 1, 'published', NOW()),

('What is the expense reimbursement turnaround time?',
 'Approved expense claims submitted by the 20th of the month are processed in the same-month payroll. Claims submitted after the 20th roll to the next month. The CM must approve expense claims within 3 working days of BD submission. Unapproved claims auto-escalate to the RM after 5 working days. Standard turnaround from CM approval to bank credit is 7 to 10 working days.',
 'policy_process', 'expense reimbursement,payroll,turnaround,CM approval', NULL, 1, 'published', NOW()),

('What is the policy on a BD directly contacting an RM or Director without going through the CM?',
 'BDs should route all escalations and requests through their CM first. Direct RM contact is permitted only for: (1) an unresolved red flag that the CM has not addressed within the SLA window, (2) a deal above Rs 15 lakh that requires RM sign-off, or (3) a personal HR matter. Direct Director contact is for exceptional situations (deal above Rs 50 lakh, cluster-level policy concern, whistleblower situation). Bypassing the CM for routine operational matters is flagged in the performance review.',
 'policy_process', 'escalation,RM,Director,CM,bypass policy', NULL, 1, 'published', NOW()),

('What is the maximum number of WhatsApp messages a BD can send to a single contact per week?',
 'STEM Learning does not set a hard cap on BD-initiated WhatsApp messages, but the standard recommended cadence is: 1 introduction message, 1 follow-up per week for active leads (cstatus 3 to 9), and 1 touch per month for dormant leads. The greetings broadcaster (Migration 036) enforces a hard cap of 3 broadcast-type messages per recipient per quarter to prevent spam. Cold outreach to new contacts must use the STEM approved intro template.',
 'policy_process', 'WhatsApp,cadence,messages,limit,greetings', NULL, 1, 'published', NOW()),

('What is the process for a BD to flag a competitor price leak?',
 'If a BD obtains credible pricing or product information about a competitor (from a school principal, a leaked document, or a peer BD), they should: (1) report it to their CM within 24 hours, (2) the CM uploads the information as a competitor_battlecard artifact in the Knowledge Repository (Migration 036), (3) the AVP reviews and publishes it to the affected cluster. BDs must not share competitor intelligence on personal WhatsApp groups. Verified competitor information is considered a KPI under BD intelligence contribution.',
 'policy_process', 'competitor,price leak,intelligence,CM,Knowledge Repository', NULL, 1, 'published', NOW()),

('What is the lost lead (cstatus 13) re-engagement policy?',
 'When a lead moves to cstatus 13 (Lost), the BD must log a structured loss reason within 24 hours. The coach agent then sets a 90-day re-engagement reminder. At day 90, the BD receives a prompt to draft a re-engagement message (greetings outbox via the coach). BDs must not attempt re-engagement before day 90 unless there is a significant change in the school situation (new principal, new budget cycle, competitor loss). Re-engagement attempts within 90 days without CM approval are flagged.',
 'policy_process', 'lost lead,cstatus 13,re-engagement,90 days', NULL, 1, 'published', NOW()),

('How should a BD handle a school that asks for a site visit before any commitment?',
 'Site visits (reference school visits) are supported but require CM pre-approval for any site outside the BD home cluster. Within the cluster, the BD can arrange a reference school visit directly with the reference school contact (who must be briefed in advance by the BD). The school visit must be logged as a tblcallevents task with meeting purpose set to reference_visit. The BD should prepare a post-visit follow-up summary within 48 hours.',
 'policy_process', 'site visit,reference school,CM approval,follow-up', NULL, 1, 'published', NOW()),

('What is the policy on recording school meetings?',
 'BDs may record school meetings only with the explicit verbal consent of all participants at the start of the meeting. Recordings are uploaded to the STEM CRM as evidence attachments within 24 hours. Recordings must not be shared outside the STEM internal system. The BD must inform the school that the recording is for STEM internal quality and training purposes only. Non-compliance is a disciplinary matter.',
 'policy_process', 'recording,meeting,consent,CRM,policy', NULL, 1, 'published', NOW()),

('What is the proposal approval gate before a BD sends a proposal?',
 'Under Migration 026 (Proposal SLA) and Migration 036 (Asset Review), a BD must: (1) submit the draft proposal for coach AI review and receive a grade of B or above, or (2) get written CM approval if the coach review is unavailable. A grade-D proposal may not be sent. A grade-C proposal requires CM sign-off. Proposals sent without going through the review gate are flagged as a red flag (flag 17) and generate a CM notification.',
 'policy_process', 'proposal,approval gate,coach review,grade,CM sign-off', NULL, 1, 'published', NOW()),

('What is the maximum credit period STEM Learning offers to schools?',
 'Standard credit terms are net 30 days from invoice date. For government schools with documented fund release schedules, STEM Learning may extend credit up to 60 days with RM approval and a post-dated cheque. Credit beyond 60 days requires Director approval and a bank guarantee. BDs must not commit to credit terms beyond net 30 days without written RM sign-off. All extended credit terms must be documented in the contract.',
 'policy_process', 'credit,payment terms,net 30,RM approval', NULL, 1, 'published', NOW()),

-- ============================================================================
-- TECH / CRM HOW-TO (10 rows)
-- ============================================================================

('How do I update the cstatus of a lead in the STEM CRM?',
 'Log in to the STEM CRM (stemapp.in), open the lead from your My Leads dashboard, tap the Stage button (currently showing the active cstatus), and select the new cstatus from the dropdown. If the new cstatus has a gate (e.g., cstatus 6 requires a verified DM contact per Migration 024), the system will block the update and show the blocking reason. After successful update, log a tblcallevents entry as the evidence of stage movement.',
 'tech_crm', 'CRM,cstatus,update,lead,stage', NULL, 1, 'published', NOW()),

('How do I submit my daily plan in the CRM?',
 'From your BD home screen, tap Day Plan in the bottom navigation. Add each planned task: select the lead, choose the action type (call, site visit, demo, proposal, follow-up), set the time, and add a brief note. Once all tasks for the next day are added, tap Submit Plan before 18:30 IST. You will receive a confirmation notification. Plans submitted after 18:30 are marked late.',
 'tech_crm', 'daily plan,submit,CRM,18:30,task', NULL, 1, 'published', NOW()),

('How do I upload a MoM in the CRM after a school meeting?',
 'After completing a school meeting, open the relevant lead in the CRM, go to the Activity tab, and tap Add MoM. Fill in: attendees (use the stakeholder contacts list if they are already on file), meeting purpose (dropdown), summary, objections and responses, next action, and next meeting date. Attach a photo or PDF if available. Tap Save. MoMs must be submitted within 2 hours of meeting end.',
 'tech_crm', 'MoM,upload,CRM,meeting,Activity', NULL, 1, 'published', NOW()),

('How do I use the FAQ search in the My Coach tab?',
 'Open the My Coach tab in the STEM BD app. Tap the FAQ search bar at the top. Type your question in plain English (e.g., "what is PMSHRI pricing" or "how do I handle a space objection"). The system searches the FAQ index by keyword and semantic match. Top results appear with a confidence score. Tap an answer to expand it. If no match is found, tap Ask Anything to submit the question to the unanswered queue for Director review.',
 'tech_crm', 'FAQ search,My Coach,Ask Anything,CRM,how-to', NULL, 1, 'published', NOW()),

('How do I submit a proposal for AI coach review?',
 'In the CRM, go to My Coach > Asset Review. Tap New Review, select asset type (Proposal), then either paste the proposal text or upload a PDF file (max 25 MB). Add the lead name and current cstatus. Tap Submit for Review. The coach returns a grade, strengths, improvements, and red flags within 30 seconds. If grade is A or A+, you will see a Send via STEM button that logs the send and marks the proposal as quality-approved.',
 'tech_crm', 'asset review,proposal,coach review,PDF,grade,Submit', NULL, 1, 'published', NOW()),

('How do I log a field expense in the CRM?',
 'Go to the Expense section in the STEM BD app (bottom navigation). Tap New Expense. Enter: expense date, category (travel, accommodation, client entertainment, stationery), amount, description, and attach a photo of the receipt. Select the related lead if applicable. Tap Submit. Your CM receives a notification and must approve within 3 working days. Check the Expense Status tab to track approval.',
 'tech_crm', 'expense,log,CRM,receipt,CM approval', NULL, 1, 'published', NOW()),

('How do I mark a coaching drill as complete?',
 'In the My Coach tab, your Today\'s Drill card shows the assigned drill. Tap Start Drill to view the drill content (video, script, or role-play prompt). After completing the drill, tap Mark Complete, select your self-rating (1 to 5 stars), and add an optional note. If your CM has also rated the drill, their rating appears alongside yours. Completed drills count toward your weekly streak badge.',
 'tech_crm', 'drill,complete,My Coach,self-rating,streak', NULL, 1, 'published', NOW()),

('How do I approve a greeting message in the Greetings tab?',
 'Open the Greetings tab in the CRM. Your queue shows pending greeting drafts, sorted by upcoming send date. Tap any draft to see 3 variants: Formal (English), Warm (English), and Regional. Select the variant you want to send, edit the text if needed (the school name and principal name are pre-filled from the CRM). Tap Approve and Queue. The message will be sent at the proposed time via WhatsApp or email. Tap Send Now to fire immediately.',
 'tech_crm', 'greetings,approve,Greetings tab,WhatsApp,draft', NULL, 1, 'published', NOW()),

('How do I view the knowledge repository artifacts shared by the AVP?',
 'Go to My Coach > What\'s New. This feed shows all artifacts published by the AVP or Director in the last 14 days, sorted by recency. Tap any card to open the artifact details: title, summary, file download, and source link. If the artifact has a Force Acknowledge badge, you must tap Read and Understood at the bottom of the article before the badge turns green. Acknowledged artifacts are removed from your pending list.',
 'tech_crm', 'knowledge repository,What\'s New,artifact,force acknowledge,My Coach', NULL, 1, 'published', NOW()),

('How do I reset my CRM password?',
 'On the STEM CRM login page (stemapp.in), tap Forgot Password. Enter your registered mobile number or email. An OTP is sent to your registered phone. Enter the OTP and set a new password (minimum 8 characters, must include 1 uppercase and 1 number). If you do not receive the OTP within 2 minutes, tap Resend OTP. For access issues not resolved by password reset, contact the CRM helpdesk at it-support@stemlearning.in.',
 'tech_crm', 'password reset,CRM,login,OTP,helpdesk', NULL, 1, 'published', NOW()),

-- ============================================================================
-- REGIONAL / GOVT SCHEME (10 rows)
-- ============================================================================

('What is PMSHRI and how does it relate to STEM labs?',
 'PM Schools for Rising India (PMSHRI) is a Government of India scheme under the Department of School Education to develop 14,500 model schools aligned with NEP 2020. Selected PMSHRI schools receive central and state government grants for infrastructure upgrades including STEM labs. STEM Learning is an empanelled vendor with several state education departments under PMSHRI procurement norms. BDs working with government schools should ask whether the school has received a PMSHRI sanction letter and the size of the STEM lab grant.',
 'regional_govt', 'PMSHRI,government scheme,NEP 2020,STEM lab,sanction letter', NULL, 1, 'published', NOW()),

('What is the Samagra Shiksha Abhiyan STEM component and can it fund our labs?',
 'Samagra Shiksha is the centrally sponsored school education scheme that merges SSA, RMSA, and TE. It includes a dedicated STEM/Tinkering component for government upper-primary and secondary schools. STEM Learning has been approved as a supplier under the Samagra Shiksha STEM Lab category in Maharashtra, Karnataka, and Rajasthan. BDs working with government school principals should confirm the block-level education officer approval process and timeline, which varies by state.',
 'regional_govt', 'Samagra Shiksha,SSA,government,funding,Maharashtra,Karnataka', NULL, 1, 'published', NOW()),

('Which state government CSR policies are relevant for STEM lab funding?',
 'Several state government policies incentivise corporate CSR spending on school STEM infrastructure: Maharashtra (50 percent VAT waiver for CSR-funded school equipment), Karnataka (CSR contributions to government schools eligible for 80G deduction without CAP), and Tamil Nadu (CSR-funded school donations counted under district collector quota). BDs should consult the CSR policy brief in the Knowledge Repository for state-specific detail and loop in the Director for large CSR deals.',
 'regional_govt', 'CSR,state government,80G,Maharashtra,Karnataka,Tamil Nadu', NULL, 1, 'published', NOW()),

('What is the STEM Learning empanelment status with the Maharashtra government?',
 'STEM Learning is empanelled with the Maharashtra State Council of Educational Research and Training (SCERT) for STEM lab supply to government schools. The empanelment is valid until March 2027. Empanelled status means government schools in Maharashtra can procure STEM labs without a fresh tender if the order value is below Rs 5 lakh per school. Above Rs 5 lakh, a limited tender process is required even with empanelment. BDs should verify the current order value cap with the CM before quoting government schools.',
 'regional_govt', 'Maharashtra,SCERT,empanelment,government,tender', NULL, 1, 'published', NOW()),

('Does STEM Learning qualify as a GeM (Government e-Marketplace) seller?',
 'Yes. STEM Learning is registered on GeM (gem.gov.in) for STEM lab kits and curriculum services. Government schools and district education offices can directly place orders through GeM using the STEM Learning catalogue. GeM pricing is fixed (not negotiable) and is listed on the platform. BDs should direct government buyers to the GeM portal for orders below Rs 25 lakh. Above Rs 25 lakh, GeM orders still require a formal indent and verification process.',
 'regional_govt', 'GeM,Government e-Marketplace,procurement,government,gem.gov.in', NULL, 1, 'published', NOW()),

('What is the STEM Learning positioning for PMSHRI grant-funded schools?',
 'PMSHRI school grants for STEM infrastructure typically range from Rs 2 lakh to Rs 10 lakh per school depending on school category and state. STEM Learning positions the STEM Discovery Lab (Rs 6 to 8 lakh range) as the primary product for PMSHRI schools. The Robotics Lab is positioned for PMSHRI schools with higher grants or multi-year tranches. BDs must check the PMSHRI sanction letter for the exact STEM lab budget line before quoting.',
 'regional_govt', 'PMSHRI,grant,Discovery Lab,Robotics Lab,sanction letter', NULL, 1, 'published', NOW()),

('How do we handle UP government school procurement for STEM labs?',
 'Uttar Pradesh government schools procure through the UP Basic Education Board (for primary) and UP Madhyamik Shiksha Parishad (for secondary). STEM Learning is in advanced empanelment discussions with UP SCERT (as of May 2026). Until empanelment is confirmed, UP government school orders must go through a formal tender process. BDs should flag any UP government school lead to the RM immediately; do not quote any price without RM guidance.',
 'regional_govt', 'Uttar Pradesh,UP,government,empanelment,SCERT', NULL, 1, 'published', NOW()),

('What is the process for getting a no-objection certificate (NOC) from state education authorities?',
 'Some state education departments require a no-objection certificate (NOC) from the block or district education officer before a private school can install an external lab programme. STEM Learning provides a standard NOC support letter that the school can submit to the officer. The letter certifies STEM Learning\'s empanelment status, curriculum alignment with NEP 2020, and compliance with data privacy norms. Contact the RM support team for the state-specific NOC letter.',
 'regional_govt', 'NOC,no-objection certificate,state education,block officer,district', NULL, 1, 'published', NOW()),

('What are the STEM lab funding options under the National Education Technology Forum?',
 'The National Education Technology Forum (NETF), constituted under NEP 2020, advises on technology procurement for schools but does not directly disburse funds. However, NETF-approved products may be fast-tracked for state procurement. STEM Learning is pursuing NETF advisory endorsement for its AI Lab and Hybrid Smart Lab. BDs should not cite NETF approval until the endorsement is confirmed; check with the Director for the latest status.',
 'regional_govt', 'NETF,NEP 2020,technology,procurement,AI Lab', NULL, 1, 'published', NOW()),

('What is the STEM approach for Kendriya Vidyalaya (KV) schools?',
 'Kendriya Vidyalaya Sangathan (KVS) procures centrally through a national tender process. Individual KV principals cannot sign purchase orders. STEM Learning has submitted a bid in the ongoing KVS STEM Lab tender (as of May 2026). BDs should not promise any individual KV principal a lab without confirming the tender outcome with the Director. However, BDs can build relationships with KV principals to position STEM Learning favourably for the post-tender implementation phase.',
 'regional_govt', 'Kendriya Vidyalaya,KVS,central tender,KV,procurement', NULL, 1, 'published', NOW());

-- ============================================================================
-- END OF SEED: faq_entry (100 rows)
-- ============================================================================
