# Rewards Program
## Ovia Health Senior Engineer Exercise
### Alex Rockwell

This was a fun assignment.

### Proposal

As the requested system is based around recording events, it seemed logical to propose an event system to capture/evaluate the data in question.  

For this I elected to use [Symfony's Event Dispatcher Component][Event dispatcher].

Once instantiated, the [Incentive Listener][Incentive Listener] will listen for the two event types:
* A user reporting a birth
* A user logging data

When dispatched, these events trigger the corresponding stubs in the [Reward Repository][Reward Repository], which presumably will be linked to the appropriate database or system responsible for reward issuance.

Examples of this behavior can be observed in the [test cases][Test class].

To feed in actual client data, the Incentive Listener could be retrofitted with an RPC server, or an existing API could make use of it by including this repository as a dependency.

### If I had more time

From an architecture perspective, it seems to me that the most important component here is capturing (and not losing) user event data.  

If we are to assume that the clients "fire and forget" their event information, then durably retaining that information until it has been successfully processed is the critical issue.

For such a bus, I would likely propose an AWS Lambda function feeding into an SQS queue, guaranteeing the user events are recorded regardless of the status of any consumer functions or their infrastructure.

A poller would then periodically inspect the queue messages and dispatch their corresponding events.  Upon successful listener completion, queue messages can then be safely purged.

Finally, criteria for trimming unneeded user event data needs to be established.  For the more complicated incentives such as logging data on five contiguous days, it would be wise to establish their event data structure as campaigns.  This would allow the data to be pruned when the campaign completes successfully or unsuccessfully, and it would better allow individual events to be used by multiple campaign tracks without conflating the data pruning logic between them. 

### How to share data externally

This somewhat depends on the mechanism in which the external entity can accept the data, and how many entities we are dealing with.

Once a campaign is complete, the de-identified data (I assume this is what was meant by the data security requirement) could either be pushed to the external entity, or stored in an analytics system available to them. 

De-identification would involve removing any PII from the related user data, while providing a deterministic identifier such as a hash or user id.

[Event dispatcher]: https://symfony.com/doc/current/components/event_dispatcher.html
[Incentive Listener]: src/IncentiveListener.php
[Reward Repository]: src/RewardRepository.php
[Test class]: tests/IncentiveListenerTest.php