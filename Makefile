single-test:
	bash ./dev/run_single_test.sh $(filter-out $@,$(MAKECMDGOALS))

test-issue:
	bash ./dev/run_single_test.sh JackrabbitImagickIssueCest

test-workaround:
	bash ./dev/run_single_test.sh JackRabbitImagickWorkaroundCest
