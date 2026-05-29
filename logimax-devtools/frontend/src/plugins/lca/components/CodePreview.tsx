import { Prism as SyntaxHighlighter } from 'react-syntax-highlighter';
import { vscDarkPlus } from 'react-syntax-highlighter/dist/esm/styles/prism';

interface CodePreviewProps {
    code: string;
    language?: string;
    className?: string;
}

export function CodePreview({ code, language = 'php', className }: CodePreviewProps) {
    if (!code) {
        return (
            <div className="p-4 text-sm text-gray-500 bg-gray-50 border rounded-md">
                No code content available.
            </div>
        );
    }

    return (
        <div className={`relative h-full overflow-hidden ${className}`}>
             <SyntaxHighlighter
                language={language}
                style={vscDarkPlus}
                customStyle={{
                    margin: 0,
                    padding: '1rem',
                    fontSize: '0.875rem',
                    lineHeight: '1.5',
                    height: '100%',
                }}
                showLineNumbers={true}
                lineNumberStyle={{ color: '#858585' }}
            >
                {code}
            </SyntaxHighlighter>
        </div>
    );
}
