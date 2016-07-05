<?php


namespace Prometheus;


use Prometheus\Storage\Adapter;

class Registry
{
    private $storageAdapter;
    /**
     * @var Gauge[]
     */
    private $gauges = array();
    /**
     * @var Counter[]
     */
    private $counters = array();
    /**
     * @var Histogram[]
     */
    private $histograms = array();

    public function __construct(Adapter $redisAdapter)
    {
        $this->storageAdapter = $redisAdapter;
    }

    /**
     * @param string $namespace e.g. cms
     * @param string $name e.g. duration_seconds
     * @param string $help e.g. The duration something took in seconds.
     * @param array $labels e.g. ['controller', 'action']
     * @return Gauge
     */
    public function registerGauge($namespace, $name, $help, $labels)
    {
        $this->gauges[Metric::metricIdentifier($namespace, $name, $labels)] = new Gauge(
            $this->storageAdapter,
            $namespace,
            $name,
            $help,
            $labels
        );
        return $this->gauges[Metric::metricIdentifier($namespace, $name, $labels)];
    }

    /**
     * @param string $namespace
     * @param string $name
     * @param array $labels e.g. ['controller', 'action']
     * @return Gauge
     */
    public function getGauge($namespace, $name, $labels)
    {
        return $this->gauges[Metric::metricIdentifier($namespace, $name, $labels)];
    }

    /**
     * @return string
     */
    public function toText()
    {
        $renderer = new RenderTextFormat();
        return $renderer->render($this->storageAdapter->fetchMetrics());
    }

    /**
     * @param string $namespace
     * @param string $name
     * @param array $labels e.g. ['controller', 'action']
     * @return Counter
     */
    public function getCounter($namespace, $name, $labels)
    {
        return $this->counters[Metric::metricIdentifier($namespace, $name, $labels)];
    }

    /**
     * @param string $namespace e.g. cms
     * @param string $name e.g. requests
     * @param string $help e.g. The number of requests made.
     * @param array $labels e.g. ['controller', 'action']
     * @return Counter
     */
    public function registerCounter($namespace, $name, $help, $labels)
    {
        $this->counters[Metric::metricIdentifier($namespace, $name, $labels)] = new Counter(
            $this->storageAdapter,
            $namespace,
            $name,
            $help,
            $labels
        );
        return $this->counters[Metric::metricIdentifier($namespace, $name, $labels)];
    }

    /**
     * @param string $namespace e.g. cms
     * @param string $name e.g. duration_seconds
     * @param string $help e.g. A histogram of the duration in seconds.
     * @param array $labels e.g. ['controller', 'action']
     * @param array $buckets e.g. [100, 200, 300]
     * @return Histogram
     */
    public function registerHistogram($namespace, $name, $help, $labels, $buckets)
    {
        $this->histograms[Metric::metricIdentifier($namespace, $name, $labels)] = new Histogram(
            $this->storageAdapter,
            $namespace,
            $name,
            $help,
            $labels,
            $buckets
        );
        return $this->histograms[Metric::metricIdentifier($namespace, $name, $labels)];
    }

    /**
     * @param string $namespace
     * @param string $name
     * @param array $labels e.g. ['controller', 'action']
     * @return Histogram
     */
    public function getHistogram($namespace, $name, $labels)
    {
        return $this->histograms[Metric::metricIdentifier($namespace, $name, $labels)];
    }
}
