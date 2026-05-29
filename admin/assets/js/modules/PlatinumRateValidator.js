/**
 * PlatinumRateValidator.js
 * 
 * Isolated Business Logic for Metal Rate Tolerances.
 * This module follows the "Strangler Fig Pattern" to safely extend legacy behavior.
 */

window.PlatinumRateValidator = {
    /**
     * Validates if the user's manual rate is within allowed global settings.
     * 
     * @param {number} manualRate - The rate entered by the user
     * @param {string} metalType - 'gold', 'silver', 'platinum'
     * @param {number} marketRate - Current Board Rate
     * @param {object} settings - Global settings object (min_platinum_tol, etc.)
     * @returns {object} { isValid: boolean, message: string }
     */
    validate: function(manualRate, metalType, marketRate, settings) {
        if (!manualRate || manualRate <= 0) return { isValid: true, message: '' }; // Allow empty/reset

        if (metalType === 'platinum') {
            const minTol = parseFloat(settings.min_platinum_tol) || 0;
            const maxTol = parseFloat(settings.max_platinum_tol) || 0;
            
            const minAllowed = marketRate - minTol;
            const maxAllowed = marketRate + maxTol;

            if (manualRate < minAllowed || manualRate > maxAllowed) {
                return {
                    isValid: false,
                    message: `Platinum Rate must be between ${minAllowed} and ${maxAllowed} (Market: ${marketRate})`
                };
            }
        }
        
        // Future: Move Gold/Silver logic here too
        
        return { isValid: true, message: 'Valid' };
    }
};
