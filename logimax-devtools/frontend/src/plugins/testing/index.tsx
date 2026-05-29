/**
 * Test Runner Plugin
 * Run and monitor tests across PHP, JavaScript, and Python
 */

import { FlaskConical, CheckCircle2, XCircle, Clock, Play } from 'lucide-react';
import type { DevToolsPlugin } from '@core/plugin-system';

// Plugin routes
const TestDashboard = () => (
  <div className="p-6">
    <div className="flex items-center justify-between mb-6">
      <div className="flex items-center gap-3">
        <div className="p-2 bg-success-50 dark:bg-success-900/20 rounded-lg">
          <FlaskConical className="w-6 h-6 text-success-600 dark:text-success-400" />
        </div>
        <div>
          <h1 className="text-2xl font-bold">Test Runner</h1>
          <p className="text-foreground-secondary">Run and monitor tests</p>
        </div>
      </div>
      <button className="flex items-center gap-2 px-4 py-2 bg-success-500 text-white rounded-lg hover:bg-success-600 transition-colors font-medium">
        <Play className="w-4 h-4" />
        Run All Tests
      </button>
    </div>

    <div className="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
      <StatusCard label="Passed" value={0} color="success" icon={CheckCircle2} />
      <StatusCard label="Failed" value={0} color="error" icon={XCircle} />
      <StatusCard label="Pending" value={0} color="warning" icon={Clock} />
      <StatusCard label="Coverage" value="0%" color="primary" icon={FlaskConical} />
    </div>

    <div className="grid grid-cols-1 lg:grid-cols-3 gap-4">
      <TestSuiteCard name="PHPUnit" language="PHP" tests={0} passed={0} />
      <TestSuiteCard name="Jest" language="JavaScript" tests={0} passed={0} />
      <TestSuiteCard name="pytest" language="Python" tests={0} passed={0} />
    </div>
  </div>
);

interface StatusCardProps {
  label: string;
  value: number | string;
  color: 'success' | 'error' | 'warning' | 'primary';
  icon: React.ComponentType<{ className?: string }>;
}

const StatusCard = ({ label, value, color, icon: Icon }: StatusCardProps) => {
  const colorClasses = {
    success: 'text-success-600 dark:text-success-400 bg-success-50 dark:bg-success-900/20',
    error: 'text-error-600 dark:text-error-400 bg-error-50 dark:bg-error-900/20',
    warning: 'text-warning-600 dark:text-warning-400 bg-warning-50 dark:bg-warning-900/20',
    primary: 'text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20',
  };

  return (
    <div className="bg-white dark:bg-neutral-800 rounded-xl border border-border p-4">
      <div className="flex items-center justify-between mb-2">
        <span className="text-sm text-foreground-secondary">{label}</span>
        <div className={`p-1.5 rounded-lg ${colorClasses[color]}`}>
          <Icon className="w-4 h-4" />
        </div>
      </div>
      <span className="text-2xl font-bold">{value}</span>
    </div>
  );
};

interface TestSuiteCardProps {
  name: string;
  language: string;
  tests: number;
  passed: number;
}

const TestSuiteCard = ({ name, language, tests, passed }: TestSuiteCardProps) => (
  <div className="bg-white dark:bg-neutral-800 rounded-xl border border-border p-4">
    <div className="flex items-center justify-between mb-4">
      <div>
        <h3 className="font-semibold">{name}</h3>
        <span className="text-sm text-foreground-secondary">{language}</span>
      </div>
      <button className="p-2 hover:bg-neutral-100 dark:hover:bg-neutral-700 rounded-lg transition-colors">
        <Play className="w-4 h-4" />
      </button>
    </div>
    <div className="flex items-center gap-4">
      <div className="flex-1">
        <div className="h-2 bg-neutral-200 dark:bg-neutral-700 rounded-full overflow-hidden">
          <div
            className="h-full bg-success-500 rounded-full transition-all"
            style={{ width: tests > 0 ? `${(passed / tests) * 100}%` : '0%' }}
          />
        </div>
      </div>
      <span className="text-sm text-foreground-secondary">
        {passed}/{tests}
      </span>
    </div>
  </div>
);

export const testingPlugin: DevToolsPlugin = {
  id: 'testing',
  name: 'Test Runner',
  description: 'Run and monitor unit tests across all languages',
  version: '1.0.0',
  icon: FlaskConical,
  routes: [
    {
      path: 'testing',
      element: <TestDashboard />,
    },
  ],
  commands: [
    {
      id: 'testing.runAll',
      name: 'Run All Tests',
      shortcut: 'ctrl+shift+t',
      handler: () => console.log('Run all tests'),
    },
  ],
};
