## **Task B — Membership Analytics Mini-Query (members portal flavor)**

Create a tiny endpoint and UI card that shows **7-day moving average** and **sparkline** for the product’s computed **unit sell price** (based on historical spot and today’s premium).

### **Data**

- Seed `spot_prices_history(metal, price_per_oz_cents, as_of)` with the last 10 days for Gold & Silver. (A seeder can just step and add variability.)
- Product price can be computed as `spot_price_history.price_per_oz_cents * product.weight_oz + premium_cents`

### **Deliverables**

- `GET /api/metrics/unit-price-ma7?sku=GOLD1OZ` → `{ dates:[…], prices_cents:[…], ma7_cents:[…] }`

- `/product/{sku}` Shows a Vue card that renders today’s price, delta vs 7-day average, and a tiny sparkline (Canvas or simple SVG). No chart lib required.

- Dashboard shows list of products with link to each product's respective `/product/{sku}` url.

### **Test**

- Deterministic seed → ma7 for day 7 equals arithmetic mean of prior 7 points (integer math, round half up).

## Task C: System Design Task: Payment Processing System

### Background

You are designing a payment processing system that handles real-time price quotes and order fulfillment for a financial services company. The system must process high-volume transactions with strict requirements for accuracy, reliability, and performance.

### Requirements

#### Functional Requirements

- Accept real-time price quotes from external pricing feeds
- Process customer orders based on quoted prices
- Handle payment authorization and capture
- Manage inventory/fulfillment constraints
- Process webhook notifications from payment providers
- Support order reconciliation and error recovery

#### Non-Functional Requirements

- **Reliability**: System must handle failures gracefully and recover from errors
- **Performance**: Low latency for quote-to-order processing (sub-second response times)
- **Scalability**: Handle high transaction volumes with horizontal scaling
- **Consistency**: Ensure data consistency across distributed components
- **Idempotency**: Prevent duplicate processing of the same transaction
- **Monitoring**: Comprehensive observability for system health and business metrics

#### Business Constraints

- Price quotes expire after a configurable time window
- Orders must be fulfilled within specified inventory constraints
- Payment processing must handle network failures and retries
- System must maintain audit trails for compliance
- Support for different payment strategies and tolerance levels

### Your Task

Design a system architecture that meets these requirements. Consider:

1. **System Components**: What services/modules would you create?
2. **Data Flow**: How do requests flow through your system?
3. **Failure Handling**: How does your system handle various failure scenarios?
4. **Scalability**: How would you scale this system horizontally?
5. **Monitoring**: What metrics and alerts would you implement?
6. **Trade-offs**: What are the key design decisions and their implications?

### Deliverables

- High-level system architecture diagram
- Component interaction flows
- Key design decisions and rationale
- Scalability and reliability considerations
- Monitoring and alerting strategy

---
